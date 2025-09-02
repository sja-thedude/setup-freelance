<?php

use Faker\Factory as Faker;
use App\Models\Vat;
use App\Repositories\VatRepository;

trait MakeVatTrait
{
    /**
     * Create fake instance of Vat and save it in database
     *
     * @param array $vatFields
     * @return Vat
     */
    public function makeVat($vatFields = [])
    {
        /** @var VatRepository $vatRepo */
        $vatRepo = App::make(VatRepository::class);
        $theme = $this->fakeVatData($vatFields);
        return $vatRepo->create($theme);
    }

    /**
     * Get fake instance of Vat
     *
     * @param array $vatFields
     * @return Vat
     */
    public function fakeVat($vatFields = [])
    {
        return new Vat($this->fakeVatData($vatFields));
    }

    /**
     * Get fake data of Vat
     *
     * @param array $vatFields
     * @return array
     */
    public function fakeVatData($vatFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'name' => $fake->word,
            'in_house' => $fake->word,
            'take_out' => $fake->word,
            'delivery' => $fake->word,
            'country_id' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $vatFields);
    }
}
