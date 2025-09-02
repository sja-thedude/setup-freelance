<?php

namespace App\Jobs;

use App\Facades\Helper;
use App\Models\Email;
use App\Models\User;
use App\Models\WorkspaceJob;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendMailSubmitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int $workspaceJobId
     */
    protected $workspaceJobId;

    /**
     * @var string $timezone
     */
    protected $timezone;

    /**
     * Create a new job instance.
     *
     * @param int $workspaceJobId
     * @param string|null $timezone
     */
    public function __construct(int $workspaceJobId, $timezone = null)
    {
        $this->workspaceJobId = $workspaceJobId;

        // Timezone from request
        if (!empty($timezone)) {
            $this->timezone = $timezone;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $job = WorkspaceJob::find($this->workspaceJobId);
        $workspace = $job->workspace;
        $locale = $job->workspace->getLocale();
        \App::setLocale($locale);
        $user = (!empty($workspace)) ? $workspace->getOwner() : new User([
            'email' => config('mail.from.address'),
            'name' => config('mail.from.name'),
        ]);

        $template = 'emails.new_job';
        $data = [
            'job' => $job->getData($this->timezone),
            'user' => $user,
        ];
        $rawContent = Helper::stripHTMLCSS(view($template, $data)->render());
        Email::create([
            'to' => empty($user) ? config('mail.from.address') : $user->email,
            'subject' => trans('mail.job.subject_new_job'),
            'content' => $rawContent,
            'locale' => $locale,
            'location' => json_encode([
                'id' => SendMailSubmitJob::class,
            ])
        ]);
        // The FROM address of the email should be the field "Afzender" in the system it's called "email_to".
        // The TO address of the email should be the field "E-mail" in the system it's called "email".
        Mail::send([
            'html' => $template,
            'raw' => $rawContent,
        ], $data, function ($m) use ($job, $user, $workspace) {
            /** @var \Illuminate\Mail\Message $m */

            // Get mail sender from workspace config
            $fromMail = config('mail.from.address');
            $fromName = config('mail.from.name');

            if (!empty($workspace->email_to)) {
                $fromMail = $workspace->email_to;
            }

            // Send mail with new job
            $m->from($fromMail, $fromName);
            $m->replyTo($job->email, $job->name);

            // Send to system mail (from config)
            $toMail = config('mail.from.address');
            $toName = config('mail.from.name');

            // Send to workspace owner
            if (!empty($user)) {
                $toMail = $user->email;
                $toName = $user->name;
            }

            $m->to($toMail, $toName);
            $m->subject(trans('mail.job.subject_new_job'));
        });
    }
}
