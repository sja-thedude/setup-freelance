<?php

namespace App\Models;

class WorkspaceAppMetaTranslation extends AppModel
{
    public $table = 'workspace_app_meta_translations';

    protected $fillable = [
        'name',
        'title',
        'description',
        'content',
        'url',
    ];
}
