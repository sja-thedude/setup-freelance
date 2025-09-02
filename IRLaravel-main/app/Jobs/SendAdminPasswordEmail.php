<?php

namespace App\Jobs;

use App\Facades\Helper;
use App\Models\Email;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendAdminPasswordEmail extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \App\Models\User $user */
    protected $user;
    protected $template;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(User $user, string $template = 'emails.send_admin_password')
    {
        $this->user = $user;
        $this->template = $template;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $locale = $this->user->getLocale();
        $data = ['user' => $this->user, 'locale' => $locale];
        $rawContent = Helper::stripHTMLCSS(view($this->template, $data)->render());
        Email::create([
            'to' => $this->user->email,
            'subject' => trans('mail.user.subject_create_admin'),
            'content' => $rawContent,
            'locale' => $locale,
            'location' => json_encode([
                'id' => SendAdminPasswordEmail::class,
            ])
        ]);
        Mail::send([
            'html' => $this->template,
            'raw' => $rawContent,
        ], $data, function ($m) {
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->to($this->user->email, $this->user->name)
                ->subject(trans('mail.user.subject_create_admin'));
        });
    }
}
