<?php

namespace App\Repositories;

use App\Models\Vat;

class VatRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'in_house',
        'take_out',
        'delivery',
        'country_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return Vat::class;
    }

    public function updateOrCreateVat($input) {
        if(empty($input['vat'])) {
            return false;
        }

        $ids = [];
        $countryId = $input['country_id'];
        
        foreach($input['vat'] as $item) {
            $id = $item['id'];
            unset($item['id']);
            $item['country_id'] = $countryId;
            $vat = $this->makeModel()->updateOrCreate([
                'id' => $id
            ], $item);
            
            $ids[] = $vat->id;
        }
        
        $this->makeModel()
            ->whereNotIn('id', $ids)
            ->where('country_id', $countryId)
            ->delete();
        
        return true;
    }
    
    public function getListByCountry($countryId = null) {
        $model = $this->makeModel();
            
        if(!empty($countryId)) {
            $model = $model->where('country_id', $countryId);
        } else {
            $model = $model->whereHas('country', function($query) {
                $query->where('code', 'be');
            });
        }
        
        return $model->get();
    }

    /**
     * Retrieve all data of repository, paginated
     * @overwrite
     * @param null|int $limit
     * @param array $columns
     * @param string $method
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate")
    {
        $request = request();
        $arrRequest = $request->all();

        // Filter
        $this->scopeQuery(function ($model) use ($request, $arrRequest) {
            /** @var \Illuminate\Database\Eloquent\Builder $model */
            $model = $model->select('vats.*');

            // Search by keyword (name)
            if ($request->has('keyword') && trim((string)$request->get('keyword') . '') != '') {
                $keyword = trim((string)$request->get('keyword') . '');
                $model = $model->where('vats.name', 'LIKE', "%{$keyword}%");
            }

            // Filter by country
            if ($request->has('country_id')) {
                $countryId = (int)$request->get('country_id');

                $model = $model->where('vats.country_id', $countryId);
            }

            return $model;
        });

        return parent::paginate($limit, $columns, $method);
    }

}
