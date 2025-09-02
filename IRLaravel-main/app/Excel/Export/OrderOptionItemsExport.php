<?php

namespace App\Excel\Export;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderOptionItemsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
	public $orders;
	
	public function __construct(
		$orders
	)
	{
		$this->orders = $orders;
	}
	
	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		$orderOptionItems = collect();
		
		foreach ($this->orders as $order)
		{
			if ($order->orderItems)
			{
				foreach ($order->orderItems as $item)
				{
					if ($item->optionItems)
					{
						foreach ($item->optionItems as $optionItem){
							$orderOptionItems->push($optionItem);
						}
					}
				}
			}
		}
		return $orderOptionItems;
	}
	
	public function getCsvSettings(): array
	{
		return [
			'delimiter' => ';',
		];
	}
	
	public function headings(): array
	{
		return [
			'Order item ID',
            'Product ID',
			'Product name',
            'Option ID',
			'Option Name',
            'Option Item ID',
			'Option Item name',
			'Price'
		];
	}
	
	public function map($row): array
	{
		return [
			$row->order_item_id,
            $row->product->id,
			$row->product->name,
            $row->option->id,
			$row->option->name,
            $row->optionItem->id,
			$row->optionItem->name,
			$row->price,
		];
	}
}
