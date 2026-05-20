{{-- resources/views/admin/events/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'الأحداث')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">قائمة الأحداث</h5>
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> إضافة حدث جديد
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>العنوان</th>
                        <th>التصنيف</th>
                        <th>الموقع</th>
                        <th>تاريخ البداية</th>
                        <th>تاريخ النهاية</th>
                        <th>حالة الحدث</th>
                        <th>حالة النشر</th>
                        <th>الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($events as $event)
                        <tr>
                            <td>{{ $event->id }}</td>
                            <td>
                                <img src="{{ Storage::url($event->main_image) }}" width="50" height="50" style="object-fit: cover; border-radius: 8px;">
                            </td>
                            <td>{{ $event->title_ar }}</td>
                            <td>
                                @if($event->category)
                                    <span class="badge" style="background-color: {{ $event->category->color }}; color: white;">
                                        {{ $event->category->name_ar }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                @if($event->locations->count() > 0)
                                    @foreach($event->locations->take(2) as $location)
                                        <span class="badge bg-secondary me-1">{{ $location->name_ar }}</span>
                                    @endforeach
                                    @if($event->locations->count() > 2)
                                        <span class="badge bg-info">+{{ $event->locations->count() - 2 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">لا توجد مواقع</span>
                                @endif
                            </td>
                            <td>{{ $event->start_date->format('Y-m-d') }}</td>
                            <td>{{ $event->end_date->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge bg-{{ $event->status_color }}">{{ $event->status_ar }}</span>
                            </td>
                            <td>
                                @if($event->is_published)
                                    <span class="badge bg-success">منشور</span>
                                @else
                                    <span class="badge bg-warning">مسودة</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="d-inline">
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

            {{ $events->links() }}
        </div>
    </div>
@endsection
