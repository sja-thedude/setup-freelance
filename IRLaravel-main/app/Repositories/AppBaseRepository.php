<?php

namespace App\Repositories;

use App\Models\Media;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class AppBaseRepository
 * @package App\Repositories
 */
class AppBaseRepository extends BaseRepository
{

    /** @var bool $error */
    public $error = false;
    /** @var array $errorMessages */
    public $errorMessages = [];

    /**
     * Record model
     * @var $record
     */
    public $record;

    /**
     * Override from parent to fix duplicate fire event when save 2 times
     *
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = \Prettus\Repository\Eloquent\BaseRepository::create($attributes);
        $this->skipPresenter($temporarySkipPresenter);

        return $this->parserResult($model);
    }

    /**
     * Override from parent to fix duplicate fire event when save 2 times
     *
     * @param array $attributes
     * @param int $id
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(array $attributes, $id)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = \Prettus\Repository\Eloquent\BaseRepository::update($attributes, $id);
        $this->skipPresenter($temporarySkipPresenter);

        return $this->parserResult($model);
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        // TODO: Implement model() method.
    }

    /**
     * Get Page in paging request
     *
     * @param \Illuminate\Http\Request $request
     * @return int
     */
    public function getPagingPage(\Illuminate\Http\Request $request)
    {
        $page = (int)$request->get('page', 1);

        return $page;
    }

    /**
     * Get Limit in paging request
     *
     * @param \Illuminate\Http\Request $request
     * @param int $default
     * @return int
     */
    public function getPagingLimit(\Illuminate\Http\Request $request, $default = 20)
    {
        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $limit = (empty($limit)) ? $default : $limit;

        return $limit;
    }

    /**
     * Parse custom filter and search
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Request
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function parserCustomFilter(\Illuminate\Http\Request $request)
    {
        $arrRequests = $request->all();
        $search = '';
        $searchFields = '';
        $searchFieldName = null;
        /** @var \App\Models\AppModel $model */
        $model = $this->makeModel();

        if (\Schema::hasColumn($model->getTable(), 'name')) {
            $searchFieldName = 'name';
        } else if (\Schema::hasColumn($model->getTable(), 'title')) {
            $searchFieldName = 'title';
        } else {
            // Default is ID column, prevent error when missing field
            $searchFieldName = $model->getKeyName();
        }

        foreach ($arrRequests as $field => $value) {
            if (!$request->has($field)) {
                // When invalid field input
                continue;
            }

            if ($field == 'keyword') {
                $search = $value;
                $searchFields = "{$searchFieldName}:like";
            } else {
                if (\Schema::hasColumn($model->getTable(), $field)) {
                    // If not found the field
                    continue;
                }

                $search .= ";{$field}:{$value}";
                $searchFields .= ";{$field}:=";
            }
        }

        // Push filter and search to request
        $request->merge([
            'searchJoin' => 'and',
            'search' => $search,
            'searchFields' => $searchFields,
        ]);

        return $request;
    }

    /**
     * @param $arrayRecord
     * @return mixed
     */
    public function saveMany($arrayRecord)
    {
        $result = $this->model->insert($arrayRecord);

        return $result;
    }

    /**
     * @param $condition
     * @param $attributes
     * @return mixed
     */
    public function updateAndWhere($condition, $attributes)
    {
        $result = $this->model->where($condition)->update($attributes);

        return $result;
    }

    /**
     * Upload file
     *
     * @param UploadedFile $file
     * @param string|null $dir
     * @return string
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function uploadFile(UploadedFile $file, $dir = null)
    {
        $modelInstance = $this->makeModel();
        $dirPrefix = 'public/';
        // Get relation file path
        $dir = $dirPrefix . (!empty($dir) ? $dir : $modelInstance->getTable());
        // Storage the $file to $dir
        $path = $file->store($dir);
        // Remove prefix folder
        $path = Str::replaceFirst($dirPrefix, '', $path);

        return $path;
    }

    /**
     * @param $model
     * @param array $arrFiles
     * @param bool $foreignType
     * @param bool $relationName
     * @return Collection
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function attachFiles($model, array $arrFiles, $foreignType = false, $relationName = false)
    {
        $collection = new Collection();

        if (!empty($arrFiles)) {
            //Delete & remove file
            if($relationName && !empty($model->{$relationName})) {
                if ($model->{$relationName}->count() > 0) {
                    if (isset($model->{$relationName}[0]) && !$model->{$relationName}->isEmpty() && $model->{$relationName}->count() > 0) {
                        foreach ($model->{$relationName} as $item) {
                            $filePath = "public/" . $item->file_path;

                            // When we edit a product/category and we upload the new picture to it,
                            // we must keep the old picture,
                            // so it will not impact the other restaurants
                            // which are using the data imported from that restaurant.
                            /*if (\Storage::exists($filePath)) {
                                @\Storage::delete($filePath);
                            }*/
                        }                    
                    } else {
                        $filePath = "public/" . $model->{$relationName}->file_path;

                        // When we edit a product/category and we upload the new picture to it,
                        // we must keep the old picture,
                        // so it will not impact the other restaurants
                        // which are using the data imported from that restaurant.
                        /*if (\Storage::exists($filePath)) {
                            @\Storage::delete($filePath);
                        }*/
                    }
                    
                    $model->{$relationName}()->delete();
                }
            }
            
            /** @var \App\Models\User $user */
            $user = \Auth::user();

            if (empty($user)) {
                // Try to with admin guard
                $user = \Auth::guard('admin')->user();
            }

            $files = $arrFiles['file'];

            /** @var \Illuminate\Http\UploadedFile $file */
            foreach ($files as $k => $attachment) {
                // Ignore if invalid file upload
                if (!($attachment instanceof \Illuminate\Http\UploadedFile)) {
                    continue;
                }

                // Upload attachment
                $path = $this->uploadFile($attachment);

                $domain = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && ! in_array(strtolower($_SERVER['HTTPS']), array( 'off', 'no' ))) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
                $fullPath = $domain.\Storage::url($path);
                
                // Create attachment
                $obj = Media::create([
                    'foreign_id'    => $model->id,
                    'foreign_model' => $this->model(),
                    'foreign_type' => !empty($foreignType) ? $foreignType : null,
                    'file_name'     => $attachment->getClientOriginalName(),
                    'file_type'     => $attachment->getMimeType(),
                    'file_size'     => $attachment->getSize(),
                    'file_path'     => $path,
                    'full_path'     => $fullPath
                ]);

                $collection->push($obj);
            }
        }

        return $collection;
    }

    /**
     * Get user logged in by JWT Auth
     *
     * @param bool $throwException
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @throws \Tymon\JWTAuth\Exceptions\JWTException|\Exception
     */
    public function getJWTAuth(bool $throwException = false)
    {
        try {
            // Get user logged in
            if ($token = \JWTAuth::getToken()) {
                return \JWTAuth::parseToken()->authenticate();
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            // Fall through to handle TokenExpiredException
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            // Fall through to handle TokenInvalidException
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            // Fall through to handle JWTException
        } catch (\Exception $e) {
            // Fall through to handle other exceptions
        }

        if ($throwException) {
            throw $e;
        }

        return null;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $model
     * @param \Illuminate\Http\Request|string|array $request
     * @return array
     */
    public function getOrderBy($model, $request)
    {
        $orderBy = '';
        $sortBy = '';

        // Order by: order_by=x&sort_by=y
        // Params sample: order_by=id&sort_by=asc
        if ($request->has('order_by')) {
            $orderBy = $request->get('order_by');

            if ($request->has('sort_by') && in_array($request->get('sort_by'), ['asc', 'desc'])) {
                $sortBy = $request->get('sort_by');
            }
        }

        return [$orderBy, $sortBy];
    }

    /**
     * Remove all non-numeric characters from a string
     *
     * @param mixed $loc
     * @return string
     */
    public function escapeLocation($loc)
    {
        if (empty($loc)) {
            return '0';
        }

        $loc = trim($loc . '');
        $result = preg_replace("/[^0-9.]/", "", $loc);

        return (string)$result;
    }

}
