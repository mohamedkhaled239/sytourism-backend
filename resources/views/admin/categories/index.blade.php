@extends('admin.layouts.app')

@section('title', 'إدارة التصنيفات')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">إدارة التصنيفات</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.categories.create', ['type' => $type]) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> إضافة تصنيف جديد
                    </a>
                </div>
            </div>

            <!-- فلاتر النوع -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.categories.index', ['type' => 'all']) }}" 
                           class="btn {{ $type == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                            جميع التصنيفات
                        </a>
                        <a href="{{ route('admin.categories.index', ['type' => 'news']) }}" 
                           class="btn {{ $type == 'news' ? 'btn-primary' : 'btn-outline-primary' }}">
                            تصنيفات الأخبار
                        </a>
                        <a href="{{ route('admin.categories.index', ['type' => 'events']) }}" 
                           class="btn {{ $type == 'events' ? 'btn-primary' : 'btn-outline-primary' }}">
                            تصنيفات الأحداث
                        </a>
                        <a href="{{ route('admin.categories.index', ['type' => 'investments']) }}" 
                           class="btn {{ $type == 'investments' ? 'btn-primary' : 'btn-outline-primary' }}">
                            تصنيفات الاستثمارات
                        </a>
                        <a href="{{ route('admin.categories.index', ['type' => 'locations']) }}" 
                           class="btn {{ $type == 'locations' ? 'btn-primary' : 'btn-outline-primary' }}">
                            تصنيفات المواقع
                        </a>
                    </div>
                </div>
            </div>

            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>الاسم</th>
                                <th>النوع</th>
                                <th>اللون</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $category->color }}; color: white;">
                                            {{ $category->name_ar }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $category->name }}</small>
                                    </td>
                                    <td>
                                        @switch($category->type)
                                            @case('news')
                                                <span class="badge bg-info">أخبار</span>
                                                @break
                                            @case('events')
                                                <span class="badge bg-success">أحداث</span>
                                                @break
                                            @case('investments')
                                                <span class="badge bg-warning">استثمارات</span>
                                                @break
                                            @case('locations')
                                                <span class="badge bg-secondary">مواقع</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div style="width: 30px; height: 20px; background-color: {{ $category->color }}; border-radius: 4px; border: 1px solid #ddd;"></div>
                                    </td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $category->id) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $categories->appends(['type' => $type])->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد تصنيفات</h5>
                    <p class="text-muted">ابدأ بإضافة تصنيف جديد</p>
                    <a href="{{ route('admin.categories.create', ['type' => $type]) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> إضافة تصنيف جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
