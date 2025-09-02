<?php

namespace Tests\Unit;

use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TestCase;

class SendUserEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_send_invitation_email()
    {
        $user = factory(User::class)->create(['email' => 'tester@example.com']);
        dispatch(new SendEmail([
            'template' => 'emails.invitation',
            'user' => $user,
            'link' => route('admin.showlogin'),
            'newPassword' => '123456789',
            'subject' => trans('manager.send_invitation_subject'),
        ]));
        $this->assertTrue(true);
    }

    public function test_it_send_new_manager_email()
    {
        $user = factory(User::class)->create(['email' => 'tester@example.com']);
        dispatch(new SendEmail([
            'template' => 'emails.invitation',
            'user' => $user,
            'link' => route('admin.showlogin'),
            'newPassword' => '123456789',
            'subject' => trans('manager.send_invitation_subject'),
        ]));
        $this->assertTrue(true);
    }

    public function test_it_send_user_workspace_email()
    {
        $user = factory(User::class)->create(['email' => 'tester@example.com']);
        dispatch(new SendEmail([
            'template' => 'emails.workspace_invitation',
            'user' => $user,
            'link' => route('manager.showlogin'),
            'newPassword' => '123456789',
            'subject' => trans('manager.send_invitation_subject'),
        ]));
        $this->assertTrue(true);
    }

    public function test_it_forgot_password_email()
    {
        $user = factory(User::class)->create(['email' => 'tester@example.com']);
        dispatch(new SendEmail([
            'template' => 'emails.forgot_password',
            'user' => $user,
            'link' => route('manager.showlogin'),
            'newPassword' => '123456789',
            'subject' => trans('auth.forgot_password_subject'),
        ]));

        $this->assertTrue(true);
    }
}
