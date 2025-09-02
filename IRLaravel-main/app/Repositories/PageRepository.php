<?php

namespace App\Repositories;

use App\Modules\ContentManager\Models\Articles;

class PageRepository extends AppBaseRepository
{
    /**
     * @var array
     */

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Articles::class;
    }
}
