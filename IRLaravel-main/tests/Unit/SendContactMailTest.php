<?php

namespace Tests\Unit;

use App\Jobs\SendContactMail;
use App\Models\Contact;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TestCase;

class SendContactMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_send_email()
    {
        $user = factory(User::class)->create(['email' => 'tester@example.com', 'name' => 'Admin']);
        $contact = Contact::create([
            'name' => 'user', 'first_name' => 'David', 'last_name' => 'Le', 'email' => 'test@example.com',
            'phone' => '011123456789',
            'address' => 'Hanoi, Hoàn Kiếm, Hanoi, Vietnam',
            'company_name' => 'VH',
            'content' => 'yyy'
        ]);
        $workspace = Workspace::create([
            'user_id' => $user->id,
            'email' => 'test@example.com',
        ]);
        SendContactMail::dispatch($contact, $workspace);
        $this->assertTrue(true);
    }
}
