<?php

use Faker\Factory as Faker;
use App\Models\Loyalty;
use App\Repositories\LoyaltyRepository;

trait MakeLoyaltyTrait
{
    /**
     * Create fake instance of Loyalty and save it in database
     *
     * @param array $loyaltyFields
     * @return Loyalty
     */
    public function makeLoyalty($loyaltyFields = [])
    {
        /** @var LoyaltyRepository $loyaltyRepo */
        $loyaltyRepo = App::make(LoyaltyRepository::class);
        $theme = $this->fakeLoyaltyData($loyaltyFields);
        return $loyaltyRepo->create($theme);
    }

    /**
     * Get fake instance of Loyalty
     *
     * @param array $loyaltyFields
     * @return Loyalty
     */
    public function fakeLoyalty($loyaltyFields = [])
    {
        return new Loyalty($this->fakeLoyaltyData($loyaltyFields));
    }

    /**
     * Get fake data of Loyalty
     *
     * @param array $loyaltyFields
     * @return array
     */
    public function fakeLoyaltyData($loyaltyFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'workspace_id' => $fake->word,
            'user_id' => $fake->word,
            'point' => $fake->word
        ], $loyaltyFields);
    }
}
