@extends('admin.layouts.app')

@section('title', 'المواقع')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">استيراد المواقع من Excel</h5>
            <div class="alert alert-light border">
                يدعم الاستيراد الآن الحقول: `governorate` و `city` و `tourism_type` و `location_types` و `categories`.
            </div>
            <form action="{{ route('admin.locations.import') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-md-8">
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv,text/csv" required>
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-upload me-2"></i> استيراد المواقع
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">قائمة المواقع ({{ $locations->total() }})</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.locations.export') }}" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i> تصدير
                    </a>
                    <a href="{{ route('admin.map.index') }}" class="btn btn-info text-white">
                        <i class="fas fa-map me-2"></i> الخريطة
                    </a>
                    <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> إضافة موقع
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>المحافظة</th>
                            <th>المدينة</th>
                            <th>نوع السياحة</th>
                            <th>نوع الموقع (دبوس)</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($locations as $location)
                            <tr>
                                <td>{{ $location->id }}</td>
                                <td>
                                    @if($location->main_image)
                                        <img src="{{ $location->main_image_url }}" alt="{{ $location->name_ar }}" class="img-thumbnail" style="width: 70px; height: 50px; object-fit: cover;">
                                    @else
                                        <span class="text-muted">بدون</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $location->name_ar }}</strong>
                                    <div class="text-muted small">{{ $location->name }}</div>
                                </td>
                                <td>{{ $location->governorate->name_ar ?? 'غير محدد' }}</td>
                                <td>{{ $location->city->name_ar ?? 'غير محدد' }}</td>
                                <td>{{ $location->tourismType->name_ar ?? 'غير محدد' }}</td>
                                <td>
                                    @if($location->locationTypes->isNotEmpty())
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            @foreach($location->locationTypes->take(3) as $lt)
                                                @if($lt->pin_image_url)
                                                    <img src="{{ $lt->pin_image_url }}"
                                                         alt="{{ $lt->name_ar }}"
                                                         title="{{ $lt->name_ar }}"
                                                         style="width:42px;height:42px;object-fit:contain;">
                                                @else
                                                    <span class="badge" style="background-color: {{ $lt->color }}; font-size:10px;">{{ $lt->name_ar }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $location->is_active ? 'success' : 'secondary' }}">
                                        {{ $location->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    @if($location->latitude && $location->longitude)
                                        <a href="{{ route('admin.map.show', $location->id) }}" class="btn btn-sm btn-info text-white">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.locations.edit', $location->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.locations.destroy', $location->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الموقع؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">لا توجد مواقع مسجلة</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
            </div>

            {{ $locations->links() }}
        </div>
    </div>
@endsection
