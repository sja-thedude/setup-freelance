<?php

use Faker\Factory as Faker;
use App\Models\PrinterJob;
use App\Repositories\PrinterJobRepository;

trait MakePrinterJobTrait
{
    /**
     * Create fake instance of PrinterJob and save it in database
     *
     * @param array $printerJobFields
     * @return PrinterJob
     */
    public function makePrinterJob($printerJobFields = [])
    {
        /** @var PrinterJobRepository $printerJobRepo */
        $printerJobRepo = App::make(PrinterJobRepository::class);
        $theme = $this->fakePrinterJobData($printerJobFields);
        return $printerJobRepo->create($theme);
    }

    /**
     * Get fake instance of PrinterJob
     *
     * @param array $printerJobFields
     * @return PrinterJob
     */
    public function fakePrinterJob($printerJobFields = [])
    {
        return new PrinterJob($this->fakePrinterJobData($printerJobFields));
    }

    /**
     * Get fake data of PrinterJob
     *
     * @param array $printerJobFields
     * @return array
     */
    public function fakePrinterJobData($printerJobFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'workspace_id' => $fake->word,
            'printer_id' => $fake->word,
            'status' => $fake->word,
            'job_type' => $fake->word,
            'foreign_model' => $fake->word,
            'foreign_id' => $fake->word,
            'content' => $fake->text,
            'meta_data' => $fake->text,
            'printed_at' => $fake->date('Y-m-d H:i:s'),
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $printerJobFields);
    }
}
