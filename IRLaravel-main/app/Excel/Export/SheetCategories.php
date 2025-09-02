<?php

namespace App\Excel\Export;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class SheetCategories implements WithTitle, WithHeadings, FromCollection, WithEvents
{
    /**
     * @var
     */
    public $categories;

    /**
     * SheetCategories constructor.
     *
     * @param $categories
     */
    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Categorieën-Categories';
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Sorting',
            'Image/foto',
            'Name/naam',
            'Category options/categorie opties',
            'Category Available?/categorie beschikbaar?',
            'Available for delivery?/beschikbaar voor levering?',
            'Favoriet friet',
            'Kokette kroket',
            'Upsell Categories/Meerverkoop categorieën',
            'Upsell Products/Meerverkoop producten',
            'Always available?/altijd beschikbaar?',
            'specific availability?/specifieke beschikbaarheid?',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'print category on sticker? / Categorie afdrukken op sticker?',
            '',
        ];
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
            'Individual/individueel',
            'Group/groep',
        ]);

        $datas[] = $subHeading;

        foreach ($this->categories as $category) {
            $categoriesSuggestion = array();
            $productsSuggestion = array();

            foreach ($category->productSuggestions as $productSuggestion) {
                if ($productSuggestion->product) {
                    $categoriesSuggestion[] = $productSuggestion->product->category_id;
                }
                if ($productSuggestion->product_id) {
                    $productsSuggestion[]   = $productSuggestion->product_id;
                }
            }

            $row = [
                $category->id,
                $category->order,
                $category->categoryAvatar ? $category->categoryAvatar->full_path : '',
                $category->translate(app()->getLocale()) ? $category->translate(app()->getLocale())->name : $category->translate('en')->name,
                implode(',', $category->categoryOptions->where('is_checked', true)->pluck('opties_id')->toArray()),
                $category->active ? "x" : "",
                $category->available_delivery ? "x" : "",
                $category->favoriet_friet ? "x" : "",
                $category->kokette_kroket ? "x" : "",
                implode(',', array_unique($categoriesSuggestion)),
                implode(',', $productsSuggestion),
                !$category->time_no_limit ? "x" : "",
                $category->time_no_limit ? "x" : "",
            ];

            $timeslots = ['', '', '', '', '', '', ''];
            foreach ($category->openTimeslots as $k => $timeslot) {
                $timeslots[$k] = $timeslot->status
                    ? "(" . date('H:i', strtotime($timeslot->start_time)) . "-" . date('H:i', strtotime($timeslot->end_time)) . ")"
                    : "";
            }

            $row = array_merge($row, $timeslots, [
                $category->individual ? "x" : "",
                $category->group ? "x" : "",
            ]);

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
                $event->sheet->mergeCells('U1:V1');
                $event->sheet->mergeCells('M1:T1');
            },
        ];
    }
}
