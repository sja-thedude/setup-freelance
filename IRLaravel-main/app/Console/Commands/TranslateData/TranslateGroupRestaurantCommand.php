<?php

namespace App\Console\Commands\TranslateData;

use App\Models\GroupRestaurant;
use Illuminate\Support\Str;

class TranslateGroupRestaurantCommand extends TranslateModelCommand
{
    /**
     * The name and signature of the console command.
     * Options:
     * --force=1 or 0
     * --forceFields=name,title,description,content,url
     *
     * @var string
     */
    protected $signature = 'translate:group_restaurant {--force=} {--forceFields=}';

    protected $description = 'Translate group restaurant fields';

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

        $this->transFile = 'grouprestaurant';
        $this->force = false;
        $this->forceFields = [
            'name',
        ];
        $this->workspaceIds = [];
    }

    public function handle()
    {
        $this->warn('Begin translation of group restaurant' . PHP_EOL);

        $this->force = $this->option('force') ? true : $this->force;
        $this->forceFields = $this->option('forceFields') ? explode(',', $this->option('forceFields')) : $this->forceFields;

        $query = GroupRestaurant::query();

        $query->chunk(static::LIMIT, function ($items) {
            foreach ($items as $item) {
                $this->info(PHP_EOL . '-> Group restaurant: ' . $item->getOriginal('name'));

                $this->translateWorkspaceApp($item);

                $this->info(PHP_EOL . '---------------------------------------');
            }
        });

        $this->info(PHP_EOL . '/End translation of group restaurant');
    }

    protected function translateWorkspaceApp(GroupRestaurant $wpaMeta)
    {
        $wpaMeta = $this->translateWorkspaceAppMeta($wpaMeta);
        $wpaMeta->save();
    }

    protected function translateWorkspaceAppMeta(GroupRestaurant $wpaMeta)
    {
        $this->info("-- #{$wpaMeta->id} - name: " . Str::limit($wpaMeta->getOriginal('name'), 20));
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
            $transKeys = array_keys(trans($this->transFile . '.default_items', [], $locale));

            foreach ($transKeys as $key) {
                $wpaMeta = $this->translateWorkspaceAppMetaFields($wpaMeta, $key, $locale, $transDefault->toArray());
            }
        }

        return $wpaMeta;
    }

    protected function translateWorkspaceAppMetaFields(GroupRestaurant $wpaMeta, string $key, string $locale, $transDefault = [])
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
