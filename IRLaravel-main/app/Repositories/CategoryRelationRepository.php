<?php
namespace App\Repositories;
use DB;
use Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\CategoryRelation;

class CategoryRelationRepository extends AppBaseRepository
{
    public function model()
    {
        return CategoryRelation::class;
    }

    public function create(array $attributes)
    {
        return parent::create($attributes);
    }

    /**
     * @param $foreignModel
     * @param $categoryId
     * @param $foreignId
     * @return mixed
     */
    public function makeInput($foreignModel, $categoryId, $foreignId)
    {
        $input['foreign_model'] = $foreignModel;
        $input['category_id'] = $categoryId;
        $input['foreign_id'] = $foreignId;

        return $input;
    }

    public function update(array $attributes, $id)
    {
        return parent::update($attributes, $id);
    }

    /**
     * @param array $attributes
     * @param $foreignModel
     * @param $foreignId
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateOrCreateCategories(array $attributes, $foreignModel, $foreignId)
    {
        foreach ($attributes as $categoryId) {
            $input = $this->makeInput($foreignModel, $categoryId, $foreignId);

            $this->create($input);
        }
    }

}
