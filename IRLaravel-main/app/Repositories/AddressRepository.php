<?php

namespace App\Repositories;

use App\Models\Address;

class AddressRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'city_id',
        'postcode',
        'address',
        'latitude',
        'longitude',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return Address::class;
    }

    /**
     * Retrieve all data of repository, paginated
     *
     * @Kurt, 2024-01-22 2:55 PM
     * - Yes searching 3 fields.
     * - Displaying "1785, Merchtem" with a group by on the "Merchtem".
     *
     * @param null|int $limit
     * @param array $columns
     * @param string $method
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate")
    {
        $request = request();

        $this->scopeQuery(function ($query) use ($request) {
            $arrRequest = $request->all();

            $query->select('addresses.*')
                ->join('cities', 'cities.id', '=', 'addresses.city_id');

            // Search by keyword
            if (array_key_exists('keyword', $arrRequest) && trim($arrRequest['keyword']) !== '') {
                $keyword = $arrRequest['keyword'];

                $query->where(function ($query) use ($keyword) {
                    $query->where('postcode', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('address', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('cities.name', 'LIKE', '%' . $keyword . '%');
                });
            }

            // Search by postcode
            if (array_key_exists('postcode', $arrRequest) && !empty($arrRequest['postcode'])) {
                $query->where('postcode', 'LIKE', '%' . $arrRequest['postcode'] . '%');
            }

            // Group by
            $query->groupBy('postcode')
                //->groupBy('address')
                ->groupBy('cities.name');

            return $query->with(['city', 'city.country']);
        });

        return parent::paginate($limit, $columns, $method);
    }

}
