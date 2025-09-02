<?php

namespace App\Jobs;

use App\Facades\Helper;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendChangeMailConfirmation extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var string $template
     */
    protected $template;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $template
     * @param string|null $locale
     */
    public function __construct(User $user, $template = 'auth.emails.change_email', $locale = null)
    {
        $this->user = $user;

        if ($this->user->platform == User::PLATFORM_BACKOFFICE) {
            $this->template = 'admin.' . $template;
        } else {
            $this->template = $template;
        }

        // Multi-language (locale)
        if (!empty($locale)) {
            \App::setLocale($locale);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = ['user' => $this->user];

        Mail::send([
            'html' => $this->template,
            'raw' => Helper::stripHTMLCSS(view($this->template, $data)->render()),
        ], $data, function ($m) {
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->to($this->user->email_tmp, $this->user->name)
                ->subject(trans('mail.user.subject_confirm_change_email'));
        });
    }
}
