<?php

namespace App\Jobs;

use App\Models\Order;
use App\Repositories\NotificationRepository;
use App\Services\SpryngSms;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

/**
 * Class PushNotification
 * @package App\Jobs
 */
class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 115;

    protected $sms;

    protected $phoneNbs;

    /**
     * Create a new job instance.
     *
     * @param array $options
     * @param string|null $locale
     */
    public function __construct($sms, $phoneNbs, $locale = null)
    {
        $this->sms = $sms;
        $this->phoneNbs = $phoneNbs;

        // Multi-language (locale)
        if (!empty($locale)) {
            \App::setLocale($locale);
        }
    }

    /**
     * Execute the job.
     *
     * @param NotificationRepository $notificationRepo
     * @return void
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function handle()
    {
        $pushNotification = new SpryngSms();
        $pushNotification->createSms($this->sms, $this->phoneNbs);
    }
}
