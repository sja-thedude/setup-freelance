<?php

namespace App\Console\Commands\TranslateData;

use App\Facades\Helper as HelperFacade;
use App\Models\AppModel;
use Illuminate\Console\Command;

class TranslateModelCommand extends Command
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
    protected $signature = 'translate:model {--force=} {--forceFields=} {--workspace=}';

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

        $this->transFile = 'enter_locale_file_name_here';
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

        // TODO: Implement translation of workspace app meta fields

        $this->info(PHP_EOL . '/End translation of workspace app meta');
    }

    // TODO: Implement translation of workspace app fields
    // protected function translateWorkspaceApp(AppModel $workspaceApp) {}

    // TODO: Implement translation of workspace app meta fields
    // protected function translateWorkspaceAppMeta(AppModel $wpaMeta) {}

    // TODO: Implement translation of workspace app meta fields
    // protected function translateWorkspaceAppMetaFields(AppModel $wpaMeta, string $key, string $locale, $transDefault = []) {}

    protected function getLanguages()
    {
        $languages = HelperFacade::getActiveLanguages();

        return $languages;
    }

    protected function getLocale()
    {
        return config('app.locale');
    }

    protected function getLocaleFallback()
    {
        return config('app.fallback_locale');
    }

    protected function getDefaultTrans(AppModel $model, string $locale = null)
    {
        $appLocale = $this->getLocale();
        $fallbackLocale = $this->getLocaleFallback();

        $trans = $model->translate($locale ?? $appLocale);

        if (!$trans) {
            $trans = $model->translate($appLocale);
        }

        if (!$trans) {
            $trans = $model->translate($fallbackLocale);
        }

        if (!$trans) {
            $trans = $this->getFirstTrans($model);
        }

        return $trans;
    }

    protected function getFirstTrans($model)
    {
        $languages = $this->getLanguages();

        foreach ($languages as $lang) {
            $trans = $model->translate($lang);

            if ($trans) {
                return $trans;
            }
        }

        return null;
    }
}
