<?php

use App\Coupon;
use App\Repositories\CouponRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CouponRepositoryTest extends TestCase
{
    use MakeCouponTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CouponRepository
     */
    protected $couponRepo;

    public function setUp()
    {
        parent::setUp();
        $this->couponRepo = App::make(CouponRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCoupon()
    {
        $coupon = $this->fakeCouponData();
        $createdCoupon = $this->couponRepo->create($coupon);
        $createdCoupon = $createdCoupon->toArray();
        $this->assertArrayHasKey('id', $createdCoupon);
        $this->assertNotNull($createdCoupon['id'], 'Created Coupon must have id specified');
        $this->assertNotNull(Coupon::find($createdCoupon['id']), 'Coupon with given id must be in DB');
        $this->assertModelData($coupon, $createdCoupon);
    }

    /**
     * @test read
     */
    public function testReadCoupon()
    {
        $coupon = $this->makeCoupon();
        $dbCoupon = $this->couponRepo->find($coupon->id);
        $dbCoupon = $dbCoupon->toArray();
        $this->assertModelData($coupon->toArray(), $dbCoupon);
    }

    /**
     * @test update
     */
    public function testUpdateCoupon()
    {
        $coupon = $this->makeCoupon();
        $fakeCoupon = $this->fakeCouponData();
        $updatedCoupon = $this->couponRepo->update($fakeCoupon, $coupon->id);
        $this->assertModelData($fakeCoupon, $updatedCoupon->toArray());
        $dbCoupon = $this->couponRepo->find($coupon->id);
        $this->assertModelData($fakeCoupon, $dbCoupon->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCoupon()
    {
        $coupon = $this->makeCoupon();
        $resp = $this->couponRepo->delete($coupon->id);
        $this->assertTrue($resp);
        $this->assertNull(Coupon::find($coupon->id), 'Coupon should not exist in DB');
    }
}
