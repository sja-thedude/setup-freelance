<?php

namespace Tests\Unit;

use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TestCase;

class SendWorkspaceEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_send_email()
    {
        $user = factory(User::class)->create(['email' => 'tester@example.com']);
        dispatch(new SendEmail([
            'template' => 'emails.workspace_invitation',
            'user' => $user,
            'link' => route('manager.showlogin'),
            'newPassword' => '123456789',
            'subject' => trans('workspace.send_invitation_subject'),
        ]));
        $this->assertTrue(true);
    }
}
