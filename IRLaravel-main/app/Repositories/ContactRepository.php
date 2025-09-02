<?php

namespace App\Repositories;

use App\Models\Contact;

class ContactRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_at',
        'updated_at',
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'company_name',
        'content'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Contact::class;
    }

    /**
     * @overwrite
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $model = parent::create($attributes);

        if (array_key_exists('workspace_id', $attributes)) {
            $this->addToWorkspace($model, (int)$attributes['workspace_id']);
        } else {
            // Send mail to Admin
            dispatch(new \App\Jobs\SendContactMailToAdmin($model, null, \App::getLocale()));
        }

        return $model;
    }

    /**
     * @param Contact $contact
     * @param int $workspaceId
     * @return Contact
     */
    public function addToWorkspace(Contact $contact, int $workspaceId)
    {
        // Attach workspace to contact
        $contact->workspaces()->attach($workspaceId);

        // Get workspace record
        /** @var \App\Models\Workspace $workspace */
        $workspace = \App\Models\Workspace::whereId($workspaceId)->active()->first();

        // Send mail
        dispatch(new \App\Jobs\SendContactMail($contact, $workspace, $workspace->getLocale()));

        return $contact;
    }

}
