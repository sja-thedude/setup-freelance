<?php

namespace App\Excel\Export;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SheetOptions implements WithTitle, WithHeadings, FromCollection
{
    /**
     * @var
     */
    private $options;

    /**
     * SheetOptions constructor.
     *
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Options';
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Sorting',
            'Name/naam',
            'Min',
            'Max',
            'Schrapping/visible as a deletion (strikethrough)',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $datas = array();
        $subHeading = array();

        for ($i = 0; $i < 6; $i++) {
            $subHeading[] = '';
        }

        $datas[] = $subHeading;

        foreach ($this->options as $option) {
            $row = [
                $option->id,
                $option->order,
                $option->translate(app()->getLocale()) ? $option->translate(app()->getLocale())->name : $option->translate('en')->name,
                $option->min ?: "0",
                $option->max,
                $option->is_ingredient_deletion ? "x" : "",
            ];
            $datas[] = $row;
        }

        return collect($datas);
    }
}
