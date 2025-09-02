<?php

namespace App\Jobs;

use App\Facades\Helper;
use App\Helpers\Order as OrderHelper;
use App\Models\Email;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendEmailSuccessOrder implements ShouldQueue
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
     * @var string
     */
    private $userLocale;

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
     * @param        $userLocale
     */
    public function __construct(
        $user,
        $dataContent,
        $template = 'layouts.emails.order-success',
        $userLocale = null
    )
    {
        $this->user = $user;
        $this->dataContent = $dataContent;
        $this->template = $template;
        $this->userLocale = $userLocale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var Order $order */
        $order = $this->dataContent['cart'];
        $workspace = $order->workspace;

        if (empty($this->user) && !empty($order->contact)) {
            // Fake user by contact when a client create new order without authentication
            $contact = $order->contact;

            if(!empty($contact->fake_email)) {
                return;
            }

            $this->user = new User([
                'email' => $contact->email,
                'name' => $contact->name,
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'phone' => $contact->phone,
            ]);
        }
        $locale = \App::getLocale();
        $eloquentUser = User::find($order->user_id);
        if ($eloquentUser) {
            $locale = $eloquentUser->getLocale();
        }
        if (!empty($this->userLocale)) {
            $locale = $this->userLocale;
        }
        \App::setLocale($locale);

        $data = [
            'userLocale'=> $locale,
            'cart'      => $order,
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
            'subject'   => $this->dataContent['subject'],
            'content_note'   => $this->dataContent['content_note'],
            'short_description' => array_get($this->dataContent, 'short_description'),
        ];

        // ITR-1146 | Generate kassabon for confirmation e-mail
        $printType = config('print.all_type.kassabon');
        $attachments = [];

        if ($order->isBuyYourSelf()) {
            $printItemProcess[] = [
                'contents' => [],
            ];

            try {
                $contents = OrderHelper::processPrint(OrderHelper::sortOptionItems($order), $printType, ($order->timezone ?? 'UTC'), true, null, $locale);
                $printItemProcess = OrderHelper::printItemProcess($printType, $order, $contents);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }

            $contents = $printItemProcess['contents'] ?? [];

            foreach ($contents as $content) {
                if ($content['type'] == 'image') {
                    $attachments[] = \Storage::disk('public')->path($content['path']);
                }
            }
        }

        $rawContent = Helper::stripHTMLCSS(view($this->template, $data)->render());
        Email::create([
            'to' => $this->user->email,
            'subject' => $this->dataContent['subject'],
            'content' => $rawContent,
            'locale' => $locale,
            'location' => json_encode([
                'id' => SendEmailSuccessOrder::class,
            ])
        ]);
        Mail::send([
            'html' => $this->template,
            'raw' => $rawContent,
        ], $data, function ($m) use ($workspace, $attachments) {
            // Get mail sender from workspace config
            $fromMail = config('mail.from.address');
            $fromName = config('mail.from.name');

            if (!empty($workspace->email_to)) {
                $fromMail = $workspace->email_to;
            }

            if (!empty($attachments)) {
                // Generate kassabon to include this in the e-mail as an attachment.
                foreach ($attachments as $attachment) {
                    $m->attach($attachment);
                }
            }

            $m->from($fromMail, $fromName);
            $m->to($this->user->email, $this->user->name)
                ->subject($this->dataContent['subject']);
        });
    }
}
