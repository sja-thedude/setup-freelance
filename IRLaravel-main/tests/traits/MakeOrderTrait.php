<?php

use Faker\Factory as Faker;
use App\Models\Order;
use App\Repositories\OrderRepository;

trait MakeOrderTrait
{
    /**
     * Create fake instance of Order and save it in database
     *
     * @param array $orderFields
     * @return Order
     */
    public function makeOrder($orderFields = [])
    {
        /** @var OrderRepository $orderRepo */
        $orderRepo = App::make(OrderRepository::class);
        $theme = $this->fakeOrderData($orderFields);
        return $orderRepo->create($theme);
    }

    /**
     * Get fake instance of Order
     *
     * @param array $orderFields
     * @return Order
     */
    public function fakeOrder($orderFields = [])
    {
        return new Order($this->fakeOrderData($orderFields));
    }

    /**
     * Get fake data of Order
     *
     * @param array $orderFields
     * @return array
     */
    public function fakeOrderData($orderFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'deleted_at' => $fake->date('Y-m-d H:i:s'),
            'workspace_id' => $fake->word,
            'user_id' => $fake->word,
            'setting_payment_id' => $fake->word,
            'open_timeslot_id' => $fake->word,
            'group_id' => $fake->word,
            'coupon_id' => $fake->word,
            'daily_id' => $fake->randomDigitNotNull,
            'payment_method' => $fake->word,
            'payment_status' => $fake->word,
            'coupon_code' => $fake->word,
            'date_time' => $fake->date('Y-m-d H:i:s'),
            'time' => $fake->word,
            'date' => $fake->word,
            'address' => $fake->word,
            'address_type' => $fake->word,
            'type' => $fake->word,
            'meta_data' => $fake->text,
            'note' => $fake->text,
            'total_price' => $fake->word,
            'currency' => $fake->word
        ], $orderFields);
    }
}
