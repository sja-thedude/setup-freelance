<?php

namespace App\Jobs;

use App\Facades\Helper;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendContactMail implements ShouldQueue
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
     */
    public function handle()
    {
        $template = 'emails.contact';
        $to = !empty($this->workspace) && !empty($this->workspace->getOwner()) ? $this->workspace->getOwner()->email : config('mail.from.address');
        $locale = !empty($this->workspace) && !empty($this->workspace->getOwner()) ? $this->workspace->getOwner()->getLocale() : \App::getLocale();
        $data = [
            'contact' => $this->contact,
            'locale' => $locale,
        ];

        $rawContent = Helper::stripHTMLCSS(view($template, $data)->render());
        Email::create([
            'to' => $to,
            'subject' => trans('mail.contact.subject_new_contact'),
            'content' => $rawContent,
            'locale' => $locale,
            'location' => json_encode([
                'id' => SendContactMail::class,
            ])
        ]);
        // The FROM address of the email should be the field "Afzender" in the system it's called "email_to".
        // The TO address of the email should be the field "E-mail" in the system it's called "email".
        Mail::send([
            'html' => $template,
            'raw' => $rawContent,
        ], $data, function ($m) {
            /** @var \Illuminate\Mail\Message $m */

            // Get mail sender from workspace config
            $fromMail = config('mail.from.address');
            $fromName = config('mail.from.name');

            if (!empty($this->workspace->email_to)) {
                $fromMail = $this->workspace->email_to;
            }

            // Send from contact human
            $m->from($fromMail, $fromName);
            $m->replyTo($this->contact->email, $this->contact->name);

            // Send to system mail (from config)
            $toMail = config('mail.from.address');
            $toName = config('mail.from.name');

            // Send to workspace owner
            if (!empty($this->workspace) && !empty($this->workspace->getOwner())) {
                $workspaceOwner = $this->workspace->getOwner();
                $toMail = $workspaceOwner->email;
                $toName = $workspaceOwner->name;
            }

            $m->to($toMail, $toName);
            $m->subject(trans('mail.contact.subject_new_contact'));
        });
    }
}
