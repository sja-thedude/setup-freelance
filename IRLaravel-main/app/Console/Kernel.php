<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ModuleCreate::class,
        Commands\ModuleController::class,
        Commands\ModuleMigrate::class,
        Commands\ModuleRoute::class,
        Commands\ThemeCreate::class,
        Commands\ThemeInstall::class,
        Commands\ThemePublish::class,
        Commands\ThemeUninstall::class,
        Commands\ContentManagerGenerator::class,
        Commands\MakeTraitCommand::class,
        Commands\MakeServiceCommand::class,
        Commands\PushNotificationPlans::class,
        Commands\EmailReminderOrder::class,
        Commands\PushNotificationReminderOrder::class,
        Commands\ConfirmOrderToManager::class,
        Commands\PrintJobScan::class,
        Commands\GenerateSixMonthCmd::class,
        Commands\PrintJobMonitor::class,
        Commands\PrintBbcodeToText::class,
        Commands\RestoreMissingOrder::class,
        Commands\PushOrderToConnectors::class,
        Commands\ScanConnectorsOrders::class,
        Commands\ImportRegionsCommand::class,
        Commands\OrderExportCommand::class,
        Commands\TranslateData\TranslateWorkspaceAppMetaCommand::class,
        Commands\TranslateData\TranslateVatCommand::class,
        Commands\TranslateData\TranslateSettingPreferenceCommand::class,
        Commands\TranslateData\TranslateGroupRestaurantCommand::class,
        Commands\HotFix\UpdateFakeEmailToContact::class,
        Commands\GenerateOrderAccessKeyWorkspace::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Scan order to print
        $schedule->command('print_job_scan:order')->everyMinute();

        // Scan order to push to connector
        $schedule->command('connectors:scan:orders')->everyMinute();

        // Scan and push notification plan by schedule
        $schedule->command('notification:push_notification_plans')->everyMinute();

        // Scan and push notification reminder order
        $schedule->command('notification:push_notification_reminder_order')->everyMinute();

        // Scan reminder order
        $schedule->command('email:reminder_order')->everyMinute();

        // Send order confirmation to manager as well.
        // In case they have internet breakdown they still have access to the order confirmation.
        // If the order is created for today, the email is sent immediately.
        // If the order is created for days in the future, the email is sent at 00:15 on that date.
        $schedule->command('email:confirm_order_to_manager')->cron('15 00 * * *');

        // Scan reminder order
        $schedule->command('time_slot:generate-six-months')->daily();

        // Check printer last jobs
        $schedule->command('print_job:monitor')->everyMinute();
    }
}
