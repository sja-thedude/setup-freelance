<?php

namespace App\Excel\Export;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class OrdersExport implements FromCollection, WithTitle, WithHeadings, WithMapping, WithCustomCsvSettings
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
    * @param $orders
    */
    public function collection()
    {
        return $this->orders;
    }
	
	public function title(): string
	{
		return 'Orders';
	}
	
	public function headings(): array
	{
		return [
			'ID',
            'Parent ID',
            'Code',
            'Extra code',
            'Customer ID',
			'Customer name',
            'Address',
			'Payment method',
			'Coupon code',
			'Coupon discount',
			'No show',
            'Is test account',
			'Order date',
            'Mollie ID',
			'Payed at',
            'Note',
			'Subtotal',
			'Total price',
			'Currency',
            'Created At',
            'Updated At',
            'Deleted At',
		];
	}
	
	public function map($row): array
	{
		return [
			$row->id,
            $row->parent_id,
            $row->code,
            $row->extra_code,
            $row->user->id ?? null,
			$row->user->name ?? null,
            $row->address,
			$row->payment_method_show,
			$row->coupon_code,
			$row->coupon_discount,
			$row->no_show,
            $row->is_test_account,
			Carbon::parse($row->date_time)->format('d/m/Y H:i'),
            $row->mollie_id,
			Carbon::parse($row->payed_at)->format('d/m/Y H:i'),
            $row->note,
			$row->subtotal,
			$row->total_price,
			$row->currency,
            Carbon::parse($row->created_at)->format('d/m/Y H:i'),
            Carbon::parse($row->updated_at)->format('d/m/Y H:i'),
            Carbon::parse($row->deleted_at)->format('d/m/Y H:i'),
		];
	}
	
	public function getCsvSettings(): array
	{
		return [
			'delimiter' => ';',
		];
	}
}
