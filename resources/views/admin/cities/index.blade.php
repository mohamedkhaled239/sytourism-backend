@extends('admin.layouts.app')

@section('title', 'المدن')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">إدارة المدن</h5>
                <a href="{{ route('admin.cities.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> إضافة مدينة
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>المدينة</th>
                        <th>المحافظة</th>
                        <th>عدد المواقع</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($cities as $city)
                        <tr>
                            <td>{{ $city->id }}</td>
                            <td>
                                <strong>{{ $city->name_ar }}</strong>
                                <div class="text-muted small">{{ $city->name }}</div>
                            </td>
                            <td>{{ $city->governorate->name_ar ?? '-' }}</td>
                            <td><span class="badge bg-info">{{ $city->locations_count }}</span></td>
                            <td>
                                <span class="badge bg-{{ $city->is_active ? 'success' : 'secondary' }}">
                                    {{ $city->is_active ? 'نشطة' : 'غير نشطة' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.cities.edit', $city->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.cities.destroy', $city->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذه المدينة؟')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">لا توجد مدن مسجلة</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $cities->links() }}
        </div>
    </div>
@endsection
