<?php

namespace Tests\Unit;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Lang;
use TestCase;

class SendAdminPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider langProvider
     */
    public function test_it_send_email(string $locale)
    {
        $user = factory(User::class)->create();
        $template = 'emails.send_admin_password';
        $data = ['user' => $user, 'locale' => $locale];

        Mail::send([
            'html' => $template,
            'raw' => Helper::stripHTMLCSS(view($template, $data)->render()),
        ], $data, function ($m) use ($user) {
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->to($user->email, $user->name)
                ->subject(Lang::get('mail.user.subject_create_admin'));
        });

        $this->assertTrue(true);
    }
    
    public function langProvider() {
        return [
            'en' => ['lang' => 'en'],
            'nl' => ['lang' => 'nl'],
            'de' => ['lang' => 'de'],
            'fr' => ['lang' => 'fr'],
        ];
    }
}
