<?php

namespace Tests\Unit;

use App\Models\Loyalty;
use App\Models\RedeemHistory;
use App\Models\Reward;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TestCase;

class SendRedeemGiftTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_send_email()
    {
        $user = factory(User::class)->create(['email' => 'tester@example.com']);
        $workspace = Workspace::create([
            'user_id' => $user->id,
            'email' => 'test@example.com',
        ]);
        $loyalty = Loyalty::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'points' => 10]);
        $reward = Reward::create(['workspace_id' => $workspace->id, 'title' => '10% Discount', 'type' => 1, 'score' => 10, 'reward' => 3, 'expire_date' => now()->addMonth()]);
        RedeemHistory::create(['reward_level_id' => $reward->id, 'loyalty_id' => $loyalty->id]);
        $timezone = 'Asia/Ho_Chi_Minh';
        dispatch(new \App\Jobs\SendEmailRewardPhysicalGift($loyalty->user, $loyalty, $reward, $timezone));
        $this->assertTrue(true);
    }
}
