<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use TestCase;

class SendChangeEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_send_email()
    {
        $this->markTestIncomplete();// Email change is no longer allowed
    }
}
