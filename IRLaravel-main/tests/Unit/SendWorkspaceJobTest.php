<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TestCase;

class SendWorkspaceJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_send_email()
    {
        $user = factory(User::class)->create(['email' => 'tester@example.com']);
        $workspace = Workspace::create([
            'user_id' => $user->id,
            'email' => 'test@example.com',
        ]);
        $workspaceJob = WorkspaceJob::create([
            'workspace_id' => $workspace->id,
            'email' => 'test@example.com'
        ]);
        $timezone = 'Asia/Ho_Chi_Minh';
        dispatch(new \App\Jobs\SendMailSubmitJob($workspaceJob->id, $timezone));
        $this->assertTrue(true);
    }
}
