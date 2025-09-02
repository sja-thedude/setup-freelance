<?php

use App\PrinterJob;
use App\Repositories\PrinterJobRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrinterJobRepositoryTest extends TestCase
{
    use MakePrinterJobTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PrinterJobRepository
     */
    protected $printerJobRepo;

    public function setUp()
    {
        parent::setUp();
        $this->printerJobRepo = App::make(PrinterJobRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePrinterJob()
    {
        $printerJob = $this->fakePrinterJobData();
        $createdPrinterJob = $this->printerJobRepo->create($printerJob);
        $createdPrinterJob = $createdPrinterJob->toArray();
        $this->assertArrayHasKey('id', $createdPrinterJob);
        $this->assertNotNull($createdPrinterJob['id'], 'Created PrinterJob must have id specified');
        $this->assertNotNull(PrinterJob::find($createdPrinterJob['id']), 'PrinterJob with given id must be in DB');
        $this->assertModelData($printerJob, $createdPrinterJob);
    }

    /**
     * @test read
     */
    public function testReadPrinterJob()
    {
        $printerJob = $this->makePrinterJob();
        $dbPrinterJob = $this->printerJobRepo->find($printerJob->id);
        $dbPrinterJob = $dbPrinterJob->toArray();
        $this->assertModelData($printerJob->toArray(), $dbPrinterJob);
    }

    /**
     * @test update
     */
    public function testUpdatePrinterJob()
    {
        $printerJob = $this->makePrinterJob();
        $fakePrinterJob = $this->fakePrinterJobData();
        $updatedPrinterJob = $this->printerJobRepo->update($fakePrinterJob, $printerJob->id);
        $this->assertModelData($fakePrinterJob, $updatedPrinterJob->toArray());
        $dbPrinterJob = $this->printerJobRepo->find($printerJob->id);
        $this->assertModelData($fakePrinterJob, $dbPrinterJob->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePrinterJob()
    {
        $printerJob = $this->makePrinterJob();
        $resp = $this->printerJobRepo->delete($printerJob->id);
        $this->assertTrue($resp);
        $this->assertNull(PrinterJob::find($printerJob->id), 'PrinterJob should not exist in DB');
    }
}
