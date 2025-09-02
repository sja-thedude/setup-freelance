<?php

namespace App\Jobs;

use App\Helpers\PictureProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPicture implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * ProcessPicture constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @throws \ImagickException
     */
    public function handle()
    {
        PictureProcessor::croppingProcess(
            $this->data['path'],
            $this->data['pathToOpen'],
            $this->data['publicPathToOpen'],
            $this->data['filename'],
            $this->data['size'],
            $this->data['module_id'],
            $this->data['type'],
            $this->data['color']
        );
    }
}
