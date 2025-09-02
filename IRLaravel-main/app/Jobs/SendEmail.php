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

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $locale;

    /**
     * Create a new job instance.
     *
     * @param $data
     * @param $locale
     */
    public function __construct($data, $locale = null)
    {
        $this->data = $data;
        $this->locale = $locale ?: $data['user']->getLocale();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!empty($this->data['template'])) {
            $template = $this->data['template'];

            switch ($template) {
                case 'emails.invitation' :
                    $this->sendEmail($this->data);
                    break;
                case 'emails.forgot_password' :
                    $this->sendEmail($this->data);
                    break;
                case 'emails.workspace_invitation' :
                    $this->sendEmail($this->data);
                    break;
            }
        }
    }

    /**
     * @param array $data
     * @throws \Throwable
     */
    private function sendEmail($data)
    {
        $template = $data['template'];
        $data['locale'] = $this->locale;
        $rawContent = Helper::stripHTMLCSS(view($template, $data)->render());
        Email::create([
            'to' => $data['user']->email,
            'subject' => $data['subject'],
            'content' => $rawContent,
            'locale' => $this->locale,
            'location' => json_encode([
                'id' => SendEmail::class,
            ])
        ]);
        Mail::send([
            'html' => $template,
            'raw' => $rawContent,
        ], $data, function ($m) use ($data) {
            $user = $data['user'];
            $m->from(config('mail.from.address'), config('mail.from.name'))
                ->to($user->email, $user->name)
                ->subject($data['subject']);
        });
    }
}
