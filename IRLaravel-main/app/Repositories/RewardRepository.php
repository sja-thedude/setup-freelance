<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\Reward;
use Carbon\Carbon;

class RewardRepository extends AppBaseRepository
{
    /**
     * Configure the Model
     */
    public function model()
    {
        return Reward::class;
    }

    /**
     * @overwrite
     * @param null $limit
     * @param string[] $columns
     * @param string $method
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate")
    {
        $request = request();
        $arrRequest = $request->all();
        $locale = \App::getLocale();

        // Filter
        $this->scopeQuery(function ($model) use ($request, $arrRequest, $locale) {
            /** @var \Illuminate\Database\Eloquent\Builder $model */

            /** @var \App\Models\Coupon $assocModel */
            $assocModel = $model->getModel();
            // Get order by from request
            list($orderBy, $sortBy) = $this->getOrderBy($model, $request);

            // Prevent duplicate field
            $model = $model->select('reward_levels.*')
                // with relations
                ->with(['workspace']);

            // Filter by workspace
            if ($request->has('workspace_id')) {
                $workspaceId = (int)$request->get('workspace_id');

                $model = $model->where('reward_levels.workspace_id', $workspaceId);
            }

            // Search by keyword: name, description
            if ($request->has('keyword') && trim($request->get('keyword') . '') != '') {
                $keyword = $request->get('keyword');

                $model = $model->where('reward_levels.title', 'LIKE', "%{$keyword}%")
                    ->orWhere('reward_levels.description', 'LIKE', "%{$keyword}%");
            }

            // Order by from request
            $model = $model->orderBy('reward_levels.score', "ASC");

            return $model;
        });

        return parent::paginate($limit, $columns, $method);
    }

    /**
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $model = parent::create($attributes);

        if (array_key_exists('files', $attributes)) {
            $this->attachFiles($model, $attributes['files'], Reward::AVATAR);
        }

        return $model;
    }

    /**
     * @param array $attributes
     * @param int   $id
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update(array $attributes, $id)
    {
        $model = parent::update($attributes, $id);

        if (array_key_exists('files', $attributes)) {
            $this->attachFiles($model, $attributes['files'], Reward::AVATAR, 'rewardAvatar');
        }

        return $model;
    }

    /**
     * Validate reward expire date
     *
     * @param Reward $reward
     * @return bool True is valid
     */
    public function validateRewardExpireDate(Reward $reward)
    {
        if (empty($reward->expire_date)) {
            return true;
        }

        /** @var \Carbon\Carbon $expireDate */
        $expireDate = (is_string($reward->expire_date)) ? Carbon::parse($reward->expire_date) : $reward->expire_date;

        return $expireDate->greaterThan(Carbon::now());
    }

    /**
     * Validate reward by products
     *
     * @param int $rewardId
     * @param array $productIds Array of product ids
     * @return array
     * @throws \Exception
     */
    public function validateRewardProducts(int $rewardId, $productIds)
    {
        $reward = \App\Models\Reward::where('id', $rewardId)
            ->first();

        // Not found reward
        if (empty($reward)) {
            throw new \Exception(trans('common.not_found'), 404);
        }

        // Validate reward by product list
        $validProducts = \App\Models\RewardProduct::where('reward_id', $reward->id)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        // Apply for categories
        $validProducts = Helper::getCategoryIds($reward, $validProducts);

        $result = [];

        foreach ($productIds as $productId) {
            $result[$productId] = in_array($productId, $validProducts);
        }

        return $result;
    }

}
