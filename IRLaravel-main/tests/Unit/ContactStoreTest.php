<?php

namespace Tests\Unit;

use App\Models\Email;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Lang;
use TestCase;

class ContactStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider langProvider
     */
    public function test_it_send_email(string $lang)
    {
        App::setLocale($lang);
        $contact = [
            'first_name' => 'David',
            'last_name' => 'Le',
            'phone' => '011123456789',
            'email' => 'test@example.com',
            'message' => 'some message',
        ];
        $rawContent = view('layouts.emails.portal_contact', ['contact' => $contact])->render();
        $to = env('DEVELOPER_EMAIL');
        Email::create([
            'to' => $to,
            'subject' => trans('frontend.contact_subject'),
            'content' => $rawContent,
            'locale' => \App::getLocale(),
            'location' => json_encode([
                'id' => 'ContactController',
            ])
        ]);
        Mail::send('layouts.emails.portal_contact', [
            'contact' => $contact
        ], function ($m) use ($to) {
            // Get mail sender from workspace config
            $fromMail = config('mail.from.address');
            $fromName = config('mail.from.name');

            $m->from($fromMail, $fromName);
            $m->to($to, 'ItsReady')
                ->subject(trans('frontend.contact_subject'));
        });
        
        $this->assertTrue(true);
    }

    public function langProvider() {
        return [
            'en' => ['lang' => 'en'],
            'de' => ['lang' => 'de'],
            'nl' => ['lang' => 'nl'],
            'fr' => ['lang' => 'fr'],
        ];
    }
}
