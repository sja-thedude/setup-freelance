<?php

namespace App\Excel\Export;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CategoryExport implements WithMultipleSheets
{
    use Exportable;

    /**
     * @var
     */
    public $categories;

    /**
     * @var
     */
    public $products;

    /**
     * @var
     */
    public $options;

    /**
     * CategoryExport constructor.
     *
     * @param $categories
     * @param $products
     * @param $options
     */
    public function __construct($categories, $products, $options)
    {
        $this->categories = $categories;
        $this->products = $products;
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            0 => new SheetCategories($this->categories),
            1 => new SheetProducts($this->products),
            2 => new SheetOptions($this->options),
            3 => new SheetOptionItems($this->options),
        ];
    }
}
