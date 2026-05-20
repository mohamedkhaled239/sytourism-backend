<?php

namespace App\Exports;

use App\Models\Location;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LocationsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Location::with(['governorate', 'tourismType'])->get();
    }

    public function headings(): array
    {
        return [
            'المعرف',
            'الاسم',
            'الاسم بالعربي',
            'العنوان بالعربي',
            'المحافظة',
            'نوع السياحة',
            'خط العرض',
            'خط الطول',
            'رقم الهاتف',
            'تاريخ الإضافة'
        ];
    }

    public function map($location): array
    {
        return [
            $location->id,
            $location->name,
            $location->name_ar,
            $location->address_ar,
            $location->governorate ? $location->governorate->name_ar : '',
            $location->tourismType ? $location->tourismType->name_ar : '',
            $location->latitude,
            $location->longitude,
            $location->phone,
            $location->created_at ? $location->created_at->format('Y-m-d H:i:s') : ''
        ];
    }
}
