<?php

namespace App\Console\Commands\TranslateData;

use App\Models\WorkspaceApp;
use App\Models\WorkspaceAppMeta;

class TranslateWorkspaceAppMetaCommand extends TranslateModelCommand
{
    /**
     * The name and signature of the console command.
     * Options:
     * --force=1 or 0
     * --forceFields=name,title,description,content,url
     * --workspace=1 -> Workspace Ids
     *
     * @var string
     */
    protected $signature = 'translate:workspace_app_meta {--force=} {--forceFields=} {--workspace=}';

    protected $description = 'Translate workspace app meta fields';

    protected $transFile;

    const LIMIT = 20;

    /**
     * The fields that should be forced to translate
     *
     * @var bool
     */
    protected $force;

    /**
     * The fields that should be forced to translate
     *
     * @var array
     */
    protected $forceFields;

    /**
     * The workspace ids
     *
     * @var array
     */
    protected $workspaceIds;

    public function __construct()
    {
        parent::__construct();

        $this->transFile = 'workspace_app_meta';
        $this->force = false;
        $this->forceFields = [
            'name',
        ];
        $this->workspaceIds = [];
    }

    public function handle()
    {
        $this->warn('Begin translation of workspace app meta' . PHP_EOL);

        $this->force = $this->option('force') ? true : $this->force;
        $this->forceFields = $this->option('forceFields') ? explode(',', $this->option('forceFields')) : $this->forceFields;
        $this->workspaceIds = $this->option('workspace') ? explode(',', $this->option('workspace')) : $this->workspaceIds;

        $query = WorkspaceApp::with(['workspace', 'workspaceAppMeta']);

        if (!empty($this->workspaceIds)) {
            $query->whereIn('workspace_id', $this->workspaceIds);
        }

        $query->chunk(static::LIMIT, function ($workspaceApps) {
            foreach ($workspaceApps as $workspaceApp) {
                $workspace = $workspaceApp->workspace;

                $this->info(PHP_EOL . '-> Workspace: ' . ($workspace->name ?? '?'));

                $this->translateWorkspaceApp($workspaceApp);

                $this->info(PHP_EOL . '---------------------------------------');
            }
        });

        $this->info(PHP_EOL . '/End translation of workspace app meta');
    }

    protected function translateWorkspaceApp(WorkspaceApp $workspaceApp)
    {
        $workspaceAppMeta = $workspaceApp->workspaceAppMeta;

        foreach ($workspaceAppMeta as $wpaMeta) {
            $wpaMeta = $this->translateWorkspaceAppMeta($wpaMeta);
            $wpaMeta->save();
        }
    }

    protected function translateWorkspaceAppMeta(WorkspaceAppMeta $wpaMeta)
    {
        $this->info("-- #{$wpaMeta->id} - key: {$wpaMeta->key} - name: " . $wpaMeta->getOriginal('name'));
        $transDefault = $this->getDefaultTrans($wpaMeta);
        $languages = $this->getLanguages();

        if (!$transDefault) {
            $transDefault = $wpaMeta->translateOrNew($this->getLocale());

            $transFields = $wpaMeta->translatedAttributes;

            foreach ($transFields as $field) {
                $transDefault->$field = $wpaMeta->getOriginal($field);
            }
        }

        foreach ($languages as $locale => $lang) {
            $wpaMeta = $this->translateWorkspaceAppMetaFields($wpaMeta, $wpaMeta->key ?? '', $locale, $transDefault->toArray());
        }

        return $wpaMeta;
    }

    protected function translateWorkspaceAppMetaFields(WorkspaceAppMeta $wpaMeta, string $key, string $locale, $transDefault = [])
    {
        $transFields = $wpaMeta->translatedAttributes;
        $transArrFile = trans($this->transFile . '.default_items.' . $key, [], $locale);
        $this->info('---- transArrFile: ' . json_encode($transArrFile, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $transArr = $transDefault ?? $transArrFile;

        $translation = $wpaMeta->translateOrNew($locale);

        if ($translation->exists && !empty($translation->getKey())) {
            // Get current translation
            $transArr = $translation->toArray();
        }

        foreach ($transFields as $field) {
            if ($this->force && in_array($field, $this->forceFields)) {
                $translation->$field = array_get($transArrFile, $field);
            } else {
                $translation->$field = array_get($transArr, $field);
            }
        }

        // Set translation
        $wpaMeta->fill([
            $locale => $translation->toArray(),
        ]);

        return $wpaMeta;
    }
}
