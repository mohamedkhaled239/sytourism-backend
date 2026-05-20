@extends('admin.layouts.app')

@section('title', 'إدارة أنواع السياحة')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">إدارة أنواع السياحة</h5>
                <a href="{{ route('admin.tourism-types.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> إضافة نوع سياحة جديد
                </a>
            </div>

            @if($tourismTypes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>النوع</th>
                                <th>الأيقونة</th>
                                <th>اللون</th>
                                <th>عدد المواقع</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tourismTypes as $type)
                                <tr>
                                    <td>{{ $type->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $type->icon }}" style="color: {{ $type->color }}; font-size: 1.2em;" title="{{ $type->name_ar }}"></i>
                                            <div class="ms-2">
                                                <strong>{{ $type->name_ar }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $type->name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $type->icon }}</code>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 30px; height: 20px; background-color: {{ $type->color }}; border-radius: 4px; border: 1px solid #ddd;" class="me-2"></div>
                                            <small>{{ $type->color }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $type->locations()->count() }}</span>
                                    </td>
                                    <td>
                                        @if($type->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>{{ $type->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.tourism-types.edit', $type->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($type->locations()->count() == 0)
                                                <form action="{{ route('admin.tourism-types.destroy', $type->id) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا النوع؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-sm btn-outline-danger" disabled 
                                                        title="لا يمكن حذف نوع مرتبط بمواقع">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $tourismTypes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-route fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد أنواع سياحة</h5>
                    <p class="text-muted">ابدأ بإضافة نوع سياحة جديد</p>
                    <a href="{{ route('admin.tourism-types.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> إضافة نوع سياحة جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
