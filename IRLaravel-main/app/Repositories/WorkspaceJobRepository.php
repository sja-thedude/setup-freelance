<?php

namespace App\Repositories;

use App\Models\WorkspaceJob;

class WorkspaceJobRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_at',
        'updated_at',
        'workspace_id',
        'name',
        'email',
        'phone',
        'content'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return WorkspaceJob::class;
    }

    /**
     * @overwrite
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $timezone = array_get($attributes, 'timezone', config('app.timezone'));

        $model = parent::create($attributes);

        $this->sendMail($model, $timezone);

        return $model;
    }

    /**
     * Send a email to the workspace owner
     *
     * @param \App\Models\WorkspaceJob $workspaceJob
     * @param string|null $timezone
     * @return mixed
     */
    public function sendMail(WorkspaceJob $workspaceJob, $timezone = null)
    {
        // Send mail
        dispatch(new \App\Jobs\SendMailSubmitJob($workspaceJob->id, $timezone));

        return $workspaceJob;
    }

}
