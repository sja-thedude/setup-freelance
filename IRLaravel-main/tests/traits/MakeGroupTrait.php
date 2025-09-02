<?php

use Faker\Factory as Faker;
use App\Models\Group;
use App\Repositories\GroupRepository;

trait MakeGroupTrait
{
    /**
     * Create fake instance of Group and save it in database
     *
     * @param array $groupFields
     * @return Group
     */
    public function makeGroup($groupFields = [])
    {
        /** @var GroupRepository $groupRepo */
        $groupRepo = App::make(GroupRepository::class);
        $theme = $this->fakeGroupData($groupFields);
        return $groupRepo->create($theme);
    }

    /**
     * Get fake instance of Group
     *
     * @param array $groupFields
     * @return Group
     */
    public function fakeGroup($groupFields = [])
    {
        return new Group($this->fakeGroupData($groupFields));
    }

    /**
     * Get fake data of Group
     *
     * @param array $groupFields
     * @return array
     */
    public function fakeGroupData($groupFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'workspace_id' => $fake->word,
            'name' => $fake->word,
            'company_name' => $fake->word,
            'company_street' => $fake->word,
            'company_number' => $fake->word,
            'company_vat_number' => $fake->word,
            'company_city' => $fake->word,
            'company_postcode' => $fake->word,
            'payment_mollie' => $fake->word,
            'payment_payconiq' => $fake->word,
            'payment_cash' => $fake->word,
            'payment_factuur' => $fake->word,
            'close_time' => $fake->word,
            'receive_time' => $fake->word,
            'type' => $fake->word,
            'contact_email' => $fake->word,
            'contact_name' => $fake->word,
            'contact_surname' => $fake->word,
            'contact_gsm' => $fake->word
        ], $groupFields);
    }
}
