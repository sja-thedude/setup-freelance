<?php

namespace Tests\Unit;

use App\Models\PrinterJob;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use TestCase;

class PrintJobMonitorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider langProvider
     */
    public function test_it_send_email(string $locale) {
        $user = factory(User::class)->create(['email' => 'tester@example.com']);
        $workspace = Workspace::create([
            'user_id' => $user->id,
            'email' => 'test@example.com',
            'language' => $locale
        ]);
        PrinterJob::create([
            'created_at' => now()->subDay(),
            'workspace_id' => $workspace->id,
            'job_type' => PrinterJob::JOB_TYPE_KASSABON,
        ]);
        $exitCode = Artisan::call('print_job:monitor');

        $this->assertEquals(0, $exitCode);
    }
    
    public function langProvider() {
        return [
            'en' => ['lang' => 'en'],
        ];
    }
}