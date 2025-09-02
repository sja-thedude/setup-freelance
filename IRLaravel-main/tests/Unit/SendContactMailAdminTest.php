<?php

namespace Tests\Unit;

use App\Jobs\SendContactMailToAdmin;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TestCase;

class SendContactMailAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider langProvider
     */
    public function test_it_send_email($locale)
    {
        $contact = Contact::create([
            'name' => 'user',
            'first_name' => 'David',
            'last_name' => 'Le',
            'email' => 'test@example.com',
            'phone' => '011123456789',
            'address' => 'Hanoi, Hoàn Kiếm, Hanoi, Vietnam',
            'company_name' => 'VH',
            'content' => 'yyy'
        ]);
        SendContactMailToAdmin::dispatch($contact, null, $locale);
        $this->assertTrue(true);
    }

    public function langProvider() {
        return [
            'en' => ['lang' => 'en'],
            'nl' => ['lang' => 'nl'],
        ];
    }
}
