<?php

namespace App\Console\Commands\TranslateData;

use App\Models\SettingPreference;
use Illuminate\Support\Str;

class TranslateSettingPreferenceCommand extends TranslateModelCommand
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
    protected $signature = 'translate:setting_preference {--force=} {--forceFields=} {--workspace=}';

    protected $description = 'Translate workspace app meta fields';

    protected $transFile = 'setting_preference';

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

        $this->transFile = 'setting_preference';
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

        $query = SettingPreference::with(['workspace']);

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

    protected function translateWorkspaceApp(SettingPreference $wpaMeta)
    {
        $wpaMeta = $this->translateWorkspaceAppMeta($wpaMeta);
        $wpaMeta->save();
    }

    protected function translateWorkspaceAppMeta(SettingPreference $wpaMeta)
    {
        $this->info("-- #{$wpaMeta->id} - key: holiday_text - content: " . Str::limit($wpaMeta->getOriginal('holiday_text'), 20));
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
            $transKeys = array_keys(trans('setting_preference.default_items', [], $locale));

            foreach ($transKeys as $key) {
                $wpaMeta = $this->translateWorkspaceAppMetaFields($wpaMeta, $key, $locale, $transDefault->toArray());
            }
        }

        return $wpaMeta;
    }

    protected function translateWorkspaceAppMetaFields(SettingPreference $wpaMeta, string $key, string $locale, $transDefault = [])
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
