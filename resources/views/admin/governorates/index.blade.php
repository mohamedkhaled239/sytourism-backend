@extends('admin.layouts.app')

@section('title', 'إدارة المحافظات')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">إدارة المحافظات</h5>
                <a href="{{ route('admin.governorates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> إضافة محافظة جديدة
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>الاسم</th>
                        <th>الكود</th>
                        <th>عدد المدن</th>
                        <th>عدد المواقع</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($governorates as $governorate)
                        <tr>
                            <td>{{ $governorate->id }}</td>
                            <td>
                                <strong>{{ $governorate->name_ar }}</strong>
                                <div class="text-muted small">{{ $governorate->name }}</div>
                            </td>
                            <td><span class="badge bg-secondary">{{ $governorate->code }}</span></td>
                            <td><span class="badge bg-primary">{{ $governorate->cities_count }}</span></td>
                            <td><span class="badge bg-info">{{ $governorate->locations_count }}</span></td>
                            <td>
                                <span class="badge bg-{{ $governorate->is_active ? 'success' : 'secondary' }}">
                                    {{ $governorate->is_active ? 'نشطة' : 'غير نشطة' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.governorates.edit', $governorate->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.cities.create') }}" class="btn btn-sm btn-outline-success" title="إضافة مدينة">
                                    <i class="fas fa-city"></i>
                                </a>
                                @if($governorate->locations_count == 0 && $governorate->cities_count == 0)
                                    <form action="{{ route('admin.governorates.destroy', $governorate->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المحافظة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">لا توجد محافظات مسجلة</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $governorates->links() }}
        </div>
    </div>
@endsection
