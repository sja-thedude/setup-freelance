<?php

namespace App\Jobs;

use App\Facades\Helper;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendContactMailToAdmin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Contact $contact
     */
    protected $contact;

    /**
     * @var Workspace $workspace
     */
    protected $workspace;

    /**
     * Create a new job instance.
     *
     * @param Contact $contact
     * @param Workspace|null $workspace
     * @param string|null $locale
     */
    public function __construct(Contact $contact, Workspace $workspace = null, string $locale = null)
    {
        $this->contact = $contact;
        $this->workspace = $workspace;

        // Multi-language (locale)
        if (!empty($locale)) {
            \App::setLocale($locale);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle()
    {
        $template = 'emails.contact_to_admin';
        $data = [
            'contact' => $this->contact
        ];
        $rawContent = Helper::stripHTMLCSS(view($template, $data)->render());
        $to = ['contact@itsready.be', 'noreply@itsready.be'];
        if (in_array(config('app.env'), ['local'])) {
            $to = [env('DEVELOPER_EMAIL')];
        }
        Email::create([
            'to' => implode(',', $to),
            'subject' => trans('mail.contact.subject_contact_to_admin'),
            'content' => $rawContent,
            'locale' => \App::getLocale(),
            'location' => json_encode([
                'id' => SendContactMailToAdmin::class,
            ])
        ]);
        Mail::send([
            'html' => $template,
            'raw' => $rawContent,
        ], $data, function (Message $m) use ($to) {
            // Send from contact human
            $m->from($this->contact->email, $this->contact->name);

            $m->to($to);
            $m->subject(trans('mail.contact.subject_contact_to_admin'));
        });
    }
}
