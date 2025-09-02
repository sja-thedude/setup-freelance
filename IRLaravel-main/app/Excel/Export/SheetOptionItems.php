<?php

namespace App\Excel\Export;

use App\Models\Option;
use App\Models\OptionItem;
use App\Models\OptionItemReference;
use App\Models\SettingConnector;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class SheetOptionItems implements WithTitle, WithHeadings, FromCollection, WithEvents
{
    /**
     * @var Option
     */
    private $options;

    /**
     * IMPORTANT: MOST BE EQUAL TO EXPORT..
     * @var int
     */
    private $numberOfOptions = 4;

    /**
     * SheetOptions constructor.
     *
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;

        // Adjust based on connectors..
        $connectorCount = count(SettingConnector::getProviders());
        $this->numberOfOptions += $connectorCount;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Option choices';
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $datas = array();
        $headings = ['', ''];

        $arrCountItem = $this->options->pluck('option_items_count')->toArray();
        $maxItemOfOption = ($arrCountItem ? max($arrCountItem) : 0) + 1;
        for ($i = 1; $i < $maxItemOfOption; $i++) {
            $headings[$i + 1] = $i;
        }

        $datas[null] = $headings;

        foreach ($this->options as $k => $option) {
            $name      = ['id' => $option->id, null => 'name'];
            $price     = ['id' => '', null => 'price'];
            $available = ['id' => '', null => 'available'];
            $master    = ['id' => '', null => 'master'];

            // Connectors..
            $connectorVariablePrefix = 'connector';
            foreach(SettingConnector::getProviders() as $providerKey => $providerName) {
                $connectorVariableName = $this->makeVariableName($providerName, $connectorVariablePrefix);
                $$connectorVariableName = ['id' => '', null => 'C ' . $providerName];
            }

            /** @var OptionItem $item */
            foreach ($option->optionItems as $item) {
                $name[]      = $item->name;
                $price[]     = $item->price ?: "0.00";
                $available[] = $item->available ? "x" : "";
                $master[]    = $item->master ? "x" : "";

                /** @var OptionItemReference[] $optionItemReferences */
                $optionItemReferences = $item->optionItemReferences;
                foreach(SettingConnector::getProviders() as $providerKey => $providerName) {
                    $currentOptionItemReference = null;
                    foreach($optionItemReferences as $optionItemReference) {
                        if($providerKey == $optionItemReference->provider) {
                            $currentOptionItemReference = $optionItemReference;
                            break;
                        }
                    }

                    $connectorVariableName = $this->makeVariableName($providerName, $connectorVariablePrefix);
                    $$connectorVariableName = array_merge($$connectorVariableName, [
                        !empty($currentOptionItemReference)
                            ? $currentOptionItemReference->remote_id
                            : ''
                    ]);
                }
            }

            $key = $this->numberOfOptions * $k;

            $datas[$key] = $name;
            $datas[$key + 1] = $price;
            $datas[$key + 2] = $available;
            $datas[$key + 3] = $master;

            $keyCounter = 3;
            foreach(SettingConnector::getProviders() as $providerKey => $providerName) {
                $keyCounter++;

                $connectorVariableName = $this->makeVariableName($providerName, $connectorVariablePrefix);
                $datas[$key + $keyCounter] = $$connectorVariableName;
            }

        }

        return collect($datas);
    }

    /**
     * @param $name
     * @param $prefix
     * @return array|string|string[]
     */
    protected function makeVariableName($name, $prefix = null) {
        $prefix = !empty($prefix)
            ? $prefix
            : '';

        return str_replace(' ', '',
            ucwords(preg_replace( '/[^a-z0-9]/i', ' ', $prefix . ' ' . $name))
        );
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                foreach ($this->options as $k => $option) {
                    $start = $this->numberOfOptions * $k + 2;
                    $end   = $start + ($this->numberOfOptions - 1);
                    $event->sheet->mergeCells("A$start:A$end");
                }
            },
        ];
    }
}