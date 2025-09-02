<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CouponApiTest extends TestCase
{
    use MakeCouponTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCoupon()
    {
        $coupon = $this->fakeCouponData();
        $this->json('POST', '/api/v1/coupons', $coupon);

        $this->assertApiResponse($coupon);
    }

    /**
     * @test
     */
    public function testReadCoupon()
    {
        $coupon = $this->makeCoupon();
        $this->json('GET', '/api/v1/coupons/'.$coupon->id);

        $this->assertApiResponse($coupon->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCoupon()
    {
        $coupon = $this->makeCoupon();
        $editedCoupon = $this->fakeCouponData();

        $this->json('PUT', '/api/v1/coupons/'.$coupon->id, $editedCoupon);

        $this->assertApiResponse($editedCoupon);
    }

    /**
     * @test
     */
    public function testDeleteCoupon()
    {
        $coupon = $this->makeCoupon();
        $this->json('DELETE', '/api/v1/coupons/'.$coupon->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/coupons/'.$coupon->id);

        $this->assertStatus(404);
    }
}
