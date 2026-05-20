{{-- resources/views/admin/investments/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'الاستثمارات')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">قائمة الاستثمارات</h5>
                <a href="{{ route('admin.investments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> إضافة استثمار جديد
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>العنوان</th>
                        <th>العنوان بالعربية</th>
                        <th>نوع الاستثمار</th>
                        <th>الحد الأدنى</th>
                        <th>الحد الأقصى</th>
                        <th>المواقع</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($investments as $investment)
                        <tr>
                            <td>{{ $investment->id }}</td>
                            <td>
                                <img src="{{ Storage::url($investment->main_image) }}" width="50" height="50" style="object-fit: cover; border-radius: 8px;">
                            </td>
                            <td>{{ $investment->title }}</td>
                            <td>{{ $investment->title_ar }}</td>
                            <td>{{ $investment->investment_type ?? 'غير محدد' }}</td>
                            <td>{{ $investment->min_investment ? number_format($investment->min_investment) : 'غير محدد' }}</td>
                            <td>{{ $investment->max_investment ? number_format($investment->max_investment) : 'غير محدد' }}</td>
                            <td>
                                @foreach($investment->locations as $location)
                                    <span class="badge bg-info me-1">{{ $location->name_ar }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($investment->is_published)
                                    <span class="badge bg-success">منشور</span>
                                @else
                                    <span class="badge bg-warning">مسودة</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.investments.edit', $investment->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.investments.destroy', $investment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{ $investments->links() }}
        </div>
    </div>
@endsection
