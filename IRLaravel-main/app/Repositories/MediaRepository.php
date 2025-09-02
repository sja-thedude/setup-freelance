<?php

namespace App\Repositories;

use App\Models\Media;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class MediaRepository.
 *
 * @package namespace App\Repositories;
 */
class MediaRepository extends AppBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Media::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
