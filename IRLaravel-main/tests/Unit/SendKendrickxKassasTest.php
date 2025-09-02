<?php

namespace Tests\Unit;

use App\Helpers\Helper;
use App\Models\Order;
use App\Models\OrderReference;
use App\Models\SettingConnector;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TestCase;

class SendKendrickxKassasTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_send_email()
    {
        $user = factory(User::class)->create(['email' => 'tester@example.com']);
        $workspace = Workspace::create([
            'user_id' => $user->id,
            'email' => 'test@example.com',
        ]);
        $connector = SettingConnector::create([
            'workspace_id' => $workspace->id,
            'provider' => 'custom'
        ]);
        $order = Order::create([
            'workspace_id' => $workspace->id,
            'total_price' => 100000,
            'user_id' => $user->id,
            'date_time' => now(),
            'date' => now()->addDay()->format('Y-m-d'),
            'time' => now()->addDay()->format('H:i'),
            'status' => Order::PAYMENT_STATUS_PAID,
            'timezone' => 'Asia/Ho_Chi_Minh',
            'code' => '013',
            'extra_code' => 1,
        ]);
        OrderReference::create(['local_id' => $order->id, 'provider' => 'custom', 'remote_id' => 1, 'order_synced_at' => now()->subDay(), 'payment_synced_at' => now()->subMinutes(10)]);
        
        $template = 'emails.hendrickx_kassas_failed';
        $data = [
            'workspace' => $workspace,
            'order' => $order,
            'orderId' => $order->id,
            'connectorId' => $connector->id,
            'attempts' => 2,
            'maxAttempts' => 3
        ];
        $isFatalError = 1;
        \Mail::send([
            'html' => $template,
            'raw' => Helper::stripHTMLCSS(view($template, $data)->render()),
        ], $data, function ($m) use ($workspace, $isFatalError) {
            /** @var \Illuminate\Mail\Message $m */

            $fromEmail = config('mail.from.address');
            $fromName = config('mail.from.name');

            $m->from($fromEmail, $fromName);

            if(!empty($workspace)) {
                $m->to($workspace->email, $workspace->manager_name);
            }
            else {
                $m->to('kurt@opwaerts.be', 'Kurt Aerts');
            }

            if(in_array(config('app.env'), ['stage', 'prod'])) {
                // When empty we already send it this mailaddress
                if(!empty($workspace)) {
                    $m->addBcc('kurt@opwaerts.be', 'Kurt Aerts'); // To make sure it works
                }

                $m->addBcc('sebastian_mathieu@hotmail.com', 'Sebastian Mathieu'); // To make sure it works
            }

            if($isFatalError) {
                $m->subject(trans('mail.hendrickx_kassas_failed.subject_fatal'));
            }
            else {
                $m->subject(trans('mail.hendrickx_kassas_failed.subject_notice'));
            }
        });
        $this->assertTrue(true);
    }
}
