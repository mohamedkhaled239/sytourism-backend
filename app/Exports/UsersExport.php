<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return User::with('favoriteEvents')->get();
    }

    public function headings(): array
    {
        return [
            'الرقم التعريفي',
            'الاسم الكامل',
            'اسم المستخدم',
            'البريد الإلكتروني',
            'رقم الهاتف',
            'البلد',
            'نوع المستخدم',
            'حالة موافقة المستثمر',
            'حالة التحقق',
            'الإشعارات',
            'عدد الأحداث المفضلة',
            'تاريخ التسجيل',
            'آخر تحديث',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->full_name,
            $user->username,
            $user->email,
            $user->phone,
            $user->country,
            $user->user_type == 'investor' ? 'مستثمر' : 'سائح',
            $user->user_type == 'investor' ? ($user->is_approved ? 'تمت الموافقة' : 'بانتظار الموافقة') : '-',
            $user->email_verified_at ? 'محقق' : 'غير محقق',
            $user->notifications_enabled ? 'مفعل' : 'معطل',
            $user->favoriteEvents->count(),
            $user->created_at->format('Y-m-d H:i:s'),
            $user->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
