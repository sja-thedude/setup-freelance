<?php

namespace App\Console\Commands\TranslateData;

use App\Models\Vat;
use Illuminate\Console\Command;

class TranslateVatCommand extends Command
{
    /**
     * The name and signature of the console command.
     * Options:
     * --force=1 or 0
     * --forceFields=name,title,description,content,url
     * --country=1 -> country Ids
     *
     * @var string
     */
    protected $signature = 'translate:vat {--force=} {--forceFields=} {--country=}';

    protected $description = 'Translate VAT fields';

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
     * The country ids
     *
     * @var array
     */
    protected $countryIds;

    /**
     * @var array
     */
    protected $defaultItems;

    public function __construct()
    {
        parent::__construct();

        $this->transFile = 'vat';
        $this->force = false;
        $this->forceFields = [
            'name',
        ];
        $this->countryIds = [];
        $this->defaultItems = [];
    }

    public function handle()
    {
        $this->warn('Begin translation of vats' . PHP_EOL);

        $this->force = $this->option('force') ? true : $this->force;
        $this->forceFields = $this->option('forceFields') ? explode(',', $this->option('forceFields')) : $this->forceFields;
        $this->countryIds = $this->option('country') ? explode(',', $this->option('country')) : $this->countryIds;

        $locale = config('app.locale');
        $this->defaultItems = $this->getVatDefaultItems($locale);
        $query = Vat::with(['country']);

        if (!empty($this->countryIds)) {
            $query->whereIn('country_id', $this->countryIds);
        }

        $query->chunk(static::LIMIT, function ($vats) {
            foreach ($vats as $vat) {
                $country = $vat->country;

                $this->info(PHP_EOL . '-> country: ' . (!empty($country) ? $country->name : '?'));

                $this->translateVat($vat);

                $this->info(PHP_EOL . '---------------------------------------');
            }
        });

        $this->info(PHP_EOL . '/End translation of vats');
    }

    private function translateVat(Vat $vat)
    {
        $this->info('-> vat: ' . $vat->name);
        $vat->key = array_get($this->defaultItems, $vat->name);
        $vat->save();
    }

    private function getVatDefaultItems(string $locale)
    {
        $defaultItems = trans($this->transFile . '.default_items', [], $locale);

        // Convert name to key mapping
        $nameToKeyMapping = collect($defaultItems)->mapWithKeys(function ($item) {
            return [$item['name'] => $item['key']];
        })->toArray();

        return $nameToKeyMapping;
    }
}
