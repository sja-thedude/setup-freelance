<?php

namespace App\Repositories;

use App\Models\GroupProduct;
use Prettus\Repository\Criteria\RequestCriteria;

class GroupProductRepository extends AppBaseRepository
{
    public function model()
    {
       return GroupProduct::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}