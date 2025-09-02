<?php

namespace App\Listeners;

use App\Events\Registered;
use App\Facades\Helper;
use App\Models\Email;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisteredSuccessful implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        // Send mail confirm
        $user = $event->user;
        $eloquentUser = User::find($user->id);
        $locale = $eloquentUser->getLocale();
        \App::setLocale($locale);

        // Options
        $options = $event->options;

        // Send email confirmation
        $this->sendEmailConfirmation($user, $options, $locale);
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array|null $options
     */
    public function sendEmailConfirmation($user, $options = [], $locale = 'nl')
    {
        $domain = array_get($options, 'domain');
        // Create confirmation link
        $routeParams = ['token' => $user->verify_token];
        if (!empty($options['group_restaurant'])) {
            $groupRestaurant = $options['group_restaurant'];
            $routeParams['Group-Token'] = $groupRestaurant->token;
        }

        $link = route('register.confirm', $routeParams);
        $link = Helper::getUrlWithDefaultRestaurant($link, $domain);
        $fullLink = $link;

        // Add redirect url if exists
        if (!empty($options['redirect_url'])) {
            $routeParams['redirect_url'] = $options['redirect_url'];
            $fullLink = Helper::getUrlWithDefaultRestaurant(route('register.confirm', $routeParams), $domain);
        }

        $template = 'auth.emails.register_confirmation';
        $data = [
            'user' => $user,
            'link' => $link,
            'full_link' => $fullLink,
        ];
        $rawContent = Helper::stripHTMLCSS(view($template, $data)->render());
        Email::create([
            'to' => $user->email,
            'subject' => trans('mail.user.subject_account_verification'),
            'content' => $rawContent,
            'locale' => $locale,
            'location' => json_encode([
                'id' => RegisteredSuccessful::class,
            ])
        ]);
        \Mail::send([
            'html' => $template,
            'raw' => $rawContent,
        ], $data, function ($m) use ($user) {
            /** @var \Illuminate\Mail\Message $m */

            $fromEmail = config('mail.from.address');
            $fromName = config('mail.from.name');

            $m->from($fromEmail, $fromName);
            $m->to($user->email, $user->name);
            $m->subject(trans('mail.user.subject_account_verification'));
        });
    }

}
