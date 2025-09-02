<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrinterJobApiTest extends TestCase
{
    use MakePrinterJobTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePrinterJob()
    {
        $printerJob = $this->fakePrinterJobData();
        $this->json('POST', '/api/v1/printerJobs', $printerJob);

        $this->assertApiResponse($printerJob);
    }

    /**
     * @test
     */
    public function testReadPrinterJob()
    {
        $printerJob = $this->makePrinterJob();
        $this->json('GET', '/api/v1/printerJobs/'.$printerJob->id);

        $this->assertApiResponse($printerJob->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePrinterJob()
    {
        $printerJob = $this->makePrinterJob();
        $editedPrinterJob = $this->fakePrinterJobData();

        $this->json('PUT', '/api/v1/printerJobs/'.$printerJob->id, $editedPrinterJob);

        $this->assertApiResponse($editedPrinterJob);
    }

    /**
     * @test
     */
    public function testDeletePrinterJob()
    {
        $printerJob = $this->makePrinterJob();
        $this->json('DELETE', '/api/v1/printerJobs/'.$printerJob->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/printerJobs/'.$printerJob->id);

        $this->assertStatus(404);
    }
}
