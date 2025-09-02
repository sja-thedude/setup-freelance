<?php

namespace App\Excel\Export;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomersExport implements FromCollection, WithTitle, WithHeadings, WithMapping, WithCustomCsvSettings
{
	protected $users;
	
	public function __construct(
		$users
	)
	{
		$this->users = $users;
	}
	
	public function collection()
	{
		return $this->users;
	}
	
	public function title(): string
	{
		return 'Customers';
	}
	
	public function headings(): array
	{
		return [
            'ID',
			'Name',
			'Email',
			'Birthday',
			'Address',
            'Phone',
            'Mobile',
            'Locale',
            'Created at',
            'Updated at',
            'Deleted at',
		];
	}
	
	public function map($row): array
	{
		return [
            $row->id,
			$row->name,
			$row->email,
			$row->birthday,
			$row->address,
            $row->phone,
            $row->gsm,
            $row->locale,
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
