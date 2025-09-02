<?php

namespace App\Jobs;

use App\Facades\Helper;
use App\Models\Email;
use App\Models\Loyalty;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendEmailRewardPhysicalGift implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var Loyalty $loyalty
     */
    protected $loyalty;

    /**
     * @var Reward $reward
     */
    protected $reward;

    /**
     * @var string $timezone
     */
    protected $timezone;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param Loyalty $loyalty
     * @param Reward $reward
     * @param string|null $timezone
     */
    public function __construct(User $user, Loyalty $loyalty, Reward $reward, $timezone = null)
    {
        $this->user = $user;
        $this->loyalty = $loyalty;
        $this->reward = $reward;

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
        // Redeem
        /** @var \App\Models\RedeemHistory $redeem */
        $redeem = \App\Models\RedeemHistory::where('loyalty_id', $this->loyalty->id)
            ->where('reward_level_id', $this->reward->id)
            ->orderBy('id', 'DESC')
            ->first();

        // Invalid data
        if (empty($redeem)) {
            return;
        }

        // Workspace info
        $workspace = $this->reward->workspace;
        $locale = $this->user->getLocale();
        \App::setLocale($locale);

        $template = 'emails.reward_physical';
        $data = [
            'user' => $this->user,
            'workspace' => $workspace,
            'loyalty' => $this->loyalty,
            'reward' => $this->reward,
            'redeem' => $redeem->getData($this->timezone),
        ];
        $rawContent = Helper::stripHTMLCSS(view($template, $data)->render());
        Email::create([
            'to' => $this->user->email,
            'subject' => trans('mail.reward.subject_physical_gift', ['title' => $this->reward->title]),
            'content' => $rawContent,
            'locale' => $locale,
            'location' => json_encode([
                'id' => SendEmailRewardPhysicalGift::class,
            ])
        ]);
        // Send mail
        Mail::send([
            'html' => $template,
            'raw' => $rawContent,
        ], $data, function ($m) use ($workspace) {
            /** @var \Illuminate\Mail\Message $m */

            // Get mail sender from workspace config
            $fromMail = config('mail.from.address');
            $fromName = config('mail.from.name');

            if (!empty($workspace->email_to)) {
                $fromMail = $workspace->email_to;
            }

            $m->from($fromMail, $fromName);
            $m->to($this->user->email, $this->user->name)
                ->subject(trans('mail.reward.subject_physical_gift', ['title' => $this->reward->title]));
        });

        /*// When send mail successfully
        if (!Mail::failures()) {
            // Clear redeem
            $this->loyalty->reward_level_id = null;
            $this->loyalty->save();
        }*/
    }
}
