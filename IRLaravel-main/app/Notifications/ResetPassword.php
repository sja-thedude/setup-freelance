<?php

namespace App\Notifications;

use App\Facades\Helper;
use App\Models\Email;
use App\Models\EmailBehavior;
use App\Models\GroupRestaurant;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Overwrite this method for include email to Reset password link in the Email
 * Class ResetPassword
 * @package App\Notifications
 */
class ResetPassword extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $routeName = 'password.reset';

        $locale = $notifiable->getLocale();
        // SonTT: disabled it, because it had not been used now.
        // I send it from domain URL, but link in email redirect to Admin reset password view
        /*if ($notifiable->platform == User::PLATFORM_BACKOFFICE) {
            $routeName = 'admin.' . $routeName;
        }*/

        app()->setLocale($locale);
        // Get email behavior
        $emailBehavior = EmailBehavior::getBehavior(
            EmailBehavior::ACTION_RESET_PASSWORD,
            $notifiable->email
        );
        $domain = (!empty($emailBehavior) && !empty($emailBehavior->workspace_id))
            ? Helper::getSubDomainOfWorkspace($emailBehavior->workspace_id) : null;

        // Create reset password link
        $routeParams = [$this->token, 'email' => $notifiable->email];
        if (!empty($emailBehavior) && isset($emailBehavior->group_restaurant_id)) {
            $groupRestaurant = GroupRestaurant::find($emailBehavior->group_restaurant_id);
            if ($groupRestaurant) {
                $routeParams['Group-Token'] = $groupRestaurant->token;
            }
        }
        $link = route($routeName, $routeParams);
        $link = Helper::getUrlWithDefaultRestaurant($link, $domain);
        $data = [
            'link' => $link,
            'full_link' => $link
        ];

        $parsedUrl = parse_url($link);
        $domain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        $domainReferer = request()->server('HTTP_REFERER');
        $redirectUrl = Helper::getRefererRedirectUrl($link, $domain, $domainReferer);

        if (!empty($redirectUrl) && $emailBehavior->origin === EmailBehavior::ORIGIN_NEXT) {
            $data['full_link'] = $link . '&redirect_url=' . urlencode($redirectUrl);
        }

        $rawContent = view('auth.emails.reset_password', $data);
        Email::create([
            'to' => $notifiable->email,
            'locale' => \App::getLocale(),
            'location' => json_encode([
                'id' => ResetPassword::class
            ]),
            'subject' => trans('mail.user.subject_reset_password'),
            'content' => $rawContent,
        ]);
        // file_put_contents(storage_path('logs/ResetPassword-toMail.log'), var_export($data, true) . PHP_EOL, FILE_APPEND);

        return (new MailMessage)
            ->line(trans('mail.user.subject_reset_password'))
            ->action(trans('passwords.sent'), $link)
            ->view('auth.emails.reset_password', $data)
            ->subject(trans('mail.user.subject_reset_password'));
    }
}
