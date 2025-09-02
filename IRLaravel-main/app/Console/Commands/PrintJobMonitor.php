<?php

namespace App\Console\Commands;

use App\Facades\Helper;
use App\Models\Email;
use App\Models\PrinterJob;
use Illuminate\Console\Command;

class PrintJobMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'print_job:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // CONFIG
        $timeBetweenMails = 15; // in minutes

        // Select not yet printed jobs within 5 minutes
        /** @var PrinterJob[] $printJobs */
        $printJobs = PrinterJob::where('created_at', '<=', date('Y-m-d H:i:s', strtotime('-5 minutes')))
            ->whereIn('status', [1, 2])
            ->groupBy('workspace_id')
            ->groupBy('job_type')
            ->orderBy('created_at', 'ASC')
            ->get();

        if(!$printJobs->isEmpty()) {
            // Send monitor alerts
            foreach($printJobs as $printJob) {
                /** @var \App\Models\Workspace|null $workspace */
                $workspace = \App\Models\Workspace::where('id', (int) $printJob->workspace_id)
                    ->active()
                    ->first();
                $locale = $workspace->getLocale();
                \App::setLocale($locale);
                $sendEmail = false;
                if(
                    (
                        $printJob->job_type == PrinterJob::JOB_TYPE_KASSABON
                        && (
                            empty($workspace->failed_at_kassabon)
                            || strtotime('+'.$timeBetweenMails.' minutes', strtotime($workspace->failed_at_kassabon)) < time()
                        )
                    )
                    || (
                        $printJob->job_type == PrinterJob::JOB_TYPE_WERKBON
                        && (
                            empty($workspace->failed_at_werkbon)
                            || strtotime('+'.$timeBetweenMails.' minutes', strtotime($workspace->failed_at_werkbon)) < time())
                    )
                    || (
                        $printJob->job_type == PrinterJob::JOB_TYPE_STICKER
                        && (
                            empty($workspace->failed_at_sticker)
                            || strtotime('+'.$timeBetweenMails.' minutes', strtotime($workspace->failed_at_sticker)) < time()
                        )
                    )
                ) {
                    $sendEmail = true;
                }

                if($sendEmail) {
                    switch($printJob->job_type) {
                        case PrinterJob::JOB_TYPE_KASSABON:
                            $workspace->failed_at_kassabon = now();
                            break;

                        case PrinterJob::JOB_TYPE_WERKBON:
                            $workspace->failed_at_werkbon = now();
                            break;

                        case PrinterJob::JOB_TYPE_STICKER:
                            $workspace->failed_at_sticker = now();
                            break;
                    }
                    $workspace->save();

                    $template = 'emails.print_job_monitor';
                    $data = [
                        'workspace' => $workspace,
                        'printJob' => $printJob,
                        'locale' => $locale,
                    ];

                    $rawContent = Helper::stripHTMLCSS(view($template, $data)->render());
                    Email::create([
                        'to' => $workspace->email,
                        'subject' => trans('mail.print_job_monitor.subject'),
                        'content' => $rawContent,
                        'locale' => $locale,
                        'location' => json_encode([
                            'id' => PrintJobMonitor::class,
                        ])
                    ]);
                    \Mail::send([
                        'html' => $template,
                        'raw' => $rawContent,
                    ], $data, function ($m) use ($workspace) {
                        /** @var \Illuminate\Mail\Message $m */

                        $fromEmail = config('mail.from.address');
                        $fromName = config('mail.from.name');

                        $m->from($fromEmail, $fromName);
                        $m->to($workspace->email, $workspace->manager_name);

                        if(in_array(config('app.env'), ['stage', 'prod'])) {
                            // $m->addBcc('kurt@opwaerts.be', 'Kurt Aerts'); // To make sure it works
                            $m->addBcc('sebastian_mathieu@hotmail.com', 'Sebastian Mathieu'); // To make sure it works
                        }

                        $m->subject(trans('mail.print_job_monitor.subject'));
                    });
                }
            }
        }
    }
}
