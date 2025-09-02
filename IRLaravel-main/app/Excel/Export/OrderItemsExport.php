<?php

namespace App\Excel\Export;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class OrderItemsExport implements FromCollection, WithTitle, WithHeadings, WithMapping, WithCustomCsvSettings
{
	/**
	 * @var
	 */
	public $orders;
	
	public function __construct($orders)
	{
		$this->orders = $orders;
	}
	
	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		$orderItems = collect();
		
		foreach ($this->orders as $order)
		{
			if ($order->orderItems)
			{
				foreach ($order->orderItems as $item)
				{
					$orderItems->push($item);
				}
			}
		}
		return $orderItems;
	}
	
	public function title(): string
	{
		return "Order items";
	}
	
	public function headings(): array
	{
		return [
			'Order ID',
            'Order item ID',
            'Product ID',
			'Product name',
			'Price',
			'Quantity',
			'Total price',
			'Coupon code',
			'Discount type',
			'Coupon discount',
            'Group discount',
            // Check if this item is applied coupon, redeem, group discount
            'Available discount',
		];
	}
	
	public function map($row): array
	{
		if ($row->coupon){
			switch ($row->coupon->discount_type)
			{
				case '1':
					$discountType = 'Fixed amount';
					break;
				case '2':
					$discountType = 'Percentage';
					break;
				default:
					$discountType = null;
			}
		}
		
		return [
			$row->order_id,
            $row->id,
            $row->product->id,
			$row->product->name,
			$row->price,
			$row->total_number,
			$row->total_price,
			$row->coupon->code ?? null,
			$discountType ?? null,
			$row->coupon_discount,
		];
	}
	
	public function getCsvSettings(): array
	{
		// TODO: Implement getCsvSettings() method.
		return [
			'delimiter' => ';',
		];
	}
}
