<?php

namespace App\Jobs;

use App\Facades\Helper;
use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendEmailReminderOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $user;

    /**
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $dataContent;

    /**
     * SendEmailReminderOrder constructor.
     *
     * @param        $user
     * @param        $dataContent
     * @param string $template
     */
    public function __construct(
        $user,
        $dataContent,
        string $template = 'layouts.emails.reminder'
    )
    {
        $this->user = $user;
        $this->dataContent = $dataContent;
        $this->template = $template;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $workspace = $this->dataContent['cart']->workspace;
        $locale = $this->user->getLocale();
        \App::setLocale($locale);
        $data = [
            'cart'      => $this->dataContent['cart'],
            'listItem'  => $this->dataContent['listItem'],
            'content1'  => $this->dataContent['content1'],
            'content2'  => $this->dataContent['content2'],
            'content3'  => $this->dataContent['content3'],
            'content4'  => $this->dataContent['content4'],
            'content5'  => $this->dataContent['content5'],
            'content6'  => $this->dataContent['content6'],
            'content7'  => $this->dataContent['content7'],
            'content8'  => $this->dataContent['content8'],
            'content9'  => $this->dataContent['content9'],
            'content10' => $this->dataContent['content10'],
            'content11' => $this->dataContent['content11'],
            'content12' => $this->dataContent['content12'],
            'subject'   => trans('mail.reminder.subject'),
            'content_note'   => $this->dataContent['content_note'],
        ];
        $rawContent = Helper::stripHTMLCSS(view($this->template, $data)->render());
        Email::create([
            'to' => $this->user->email,
            'subject' => trans('mail.reminder.subject'),
            'content' => $rawContent,
            'locale' => $locale,
            'location' => json_encode([
                'id' => SendEmailReminderOrder::class,
            ])
        ]);
        Mail::send([
            'html' => $this->template,
            'raw' => $rawContent,
        ], $data, function ($m) use ($workspace) {
            // Get mail sender from workspace config
            $fromMail = config('mail.from.address');
            $fromName = config('mail.from.name');

            if (!empty($workspace->email_to)) {
                $fromMail = $workspace->email_to;
            }

            $m->from($fromMail, $fromName);
            $m->to($this->user->email, $this->user->name)
                ->subject(trans('mail.reminder.subject'));
        });
    }
}
