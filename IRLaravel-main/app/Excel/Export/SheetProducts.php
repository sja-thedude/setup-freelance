<?php

namespace App\Excel\Export;

use App\Models\Product;
use App\Models\ProductReference;
use App\Models\SettingConnector;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class SheetProducts implements WithTitle, WithHeadings, FromCollection, WithEvents
{
    /**
     * @var
     */
    private $products;

    /**
     * SheetProducts constructor.
     *
     * @param $products
     */
    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Producten-Products';
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $headings = [
            'ID',
            'Sorting',
            'Image/foto',
            'Name/naam',
            'beschrijving/description',
            'price incl VAT/prijs incl btw',
            'VAT type/BTW type',
            'Category/categorie',
            'Exclude from category options?uitsluiten van categorie-opties?',
            'product options/productopties',
            'Product Available?/product beschikbaar?',
            'Always available?/altijd beschikbaar?',
            'specific availability?/specifieke beschikbaarheid?',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Labels',
            '',
            '',
            '',
            '',
            'naam/name',
            'Allergenen/allergens',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Connectors',
        ];

        // Add one field per connector
        foreach(SettingConnector::getProviders() as $providerKey => $providerName) {
            $headings[] = '';
        }

        return $headings;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $datas = array();
        $subHeading = array();

        for ($i = 0; $i < 12; $i++) {
            $subHeading[] = '';
        }

        $subHeading = array_merge($subHeading, [
            'Enabled?',
            'Monday/m',
            'Tuesday/d',
            'W/w',
            'T/d',
            'F/v',
            'S/z',
            'S/z',
            'vegy',
            'vegan',
            'spicy',
            'new',
            'promo',
            '',
            'ei',
            'gluten',
            'lupine',
            'melk',
            'mosterd',
            "pinda's",
            'schaaldieren',
            'soja',
            'vis',
            'weekdieren',
            'zwaveldioxide',
            'noten',
            'selderij',
            'sesamzaad',
        ]);

        // Add subheading per connector
        foreach(SettingConnector::getProviders() as $providerKey => $providerName) {
            $subHeading[] = $providerName;
        }

        $datas[] = $subHeading;

        /** @var Product $product */
        foreach ($this->products as $product) {
            $objTrans = $product->translate(app()->getLocale());
            $nameProduct = $objTrans ? $objTrans->name : $product->translate('en')->name;

            $row = [
                $product->id,
                $product->order,
                $product->productAvatar ? $product->productAvatar->full_path : '',
                $nameProduct,
                $objTrans ? $objTrans->description : $product->translate('en')->description,
                $product->price,
                $product->vat_id,
                $product->category_id,
                $product->use_category_option ? "x" : "",
                implode(',', $product->productOptions->where('is_checked', true)->pluck('opties_id')->toArray()),
                $product->active ? "x" : "",
                !$product->time_no_limit ? "x" : "",
                $product->time_no_limit ? "x" : "",
            ];

            $timeslots = ['', '', '', '', '', '', ''];
            foreach ($product->openTimeslots as $k => $timeslot) {
                $timeslots[$k] = $timeslot->status
                    ? "(" . date('H:i', strtotime($timeslot->start_time)) . "-" . date('H:i', strtotime($timeslot->end_time)) . ")"
                    : "";
            }

            $labels = ['', '', '', '', ''];
            $listLabel = $product->productLabels->sortBy('type');
            foreach ($listLabel as $k => $lb) {
                $labels[$k] = $lb->type && $lb->active ? "x" : "";
            }

            $row = array_merge($row, $timeslots, $labels);

            $row[] = $nameProduct;

            $allergens = [1, 2, 3, 4, 5, 7, 8, 11, 12, 13, 14, 6, 9, 10];
            $listLabel = $product->productAllergenens->pluck('allergenen_id')->toArray();
            foreach ($allergens as $k => $allergen) {
                $allergens[$k] = in_array($allergen, $listLabel) ? "x" : "";
            }

            $row = array_merge($row, $allergens);

            // Product references
            // @todo we can later optimize this to do one query and not a query per product..
            /** @var ProductReference[] $productReferences */
            $productReferences = $product->productReferences;
            foreach(SettingConnector::getProviders() as $providerKey => $providerName) {
                $currentProductReference = null;
                foreach($productReferences as $productReference) {
                    if($providerKey == $productReference->provider) {
                        $currentProductReference = $productReference;
                        break;
                    }
                }

                $row[] = !empty($currentProductReference->remote_id)
                    ? $currentProductReference->remote_id
                    : '';
            }

            $datas[] = $row;
        }

        return collect($datas);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Get connector letters
                $connectorCount = count(SettingConnector::getProviders());
                $connectorStart = 'AO';
                $connectorEnd = $connectorStart;
                for($i = 0; $i < $connectorCount; $i++) {
                    $connectorEnd++;
                };

                $event->sheet->mergeCells('M1:T1');
                $event->sheet->mergeCells('U1:Y1');
                $event->sheet->mergeCells('AA1:AN1');
                $event->sheet->mergeCells($connectorStart.'1:'.$connectorEnd.'1');
            },
        ];
    }
}
