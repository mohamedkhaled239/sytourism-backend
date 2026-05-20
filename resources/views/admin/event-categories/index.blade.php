{{-- resources/views/admin/event-categories/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'تصنيفات الأحداث')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">قائمة التصنيفات</h5>
                <a href="{{ route('admin.event-categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> إضافة تصنيف جديد
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الاسم بالعربية</th>
                        <th>اللون</th>
                        <th>عدد الأحداث</th>
                        <th>الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->name_ar }}</td>
                            <td>
                            <span class="badge" style="background-color: {{ $category->color }};">
                                {{ $category->color }}
                            </span>
                            </td>
                            <td>{{ $category->events_count }}</td>
                            <td>
                                <a href="{{ route('admin.event-categories.edit', $category->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($category->events_count == 0)
                                    <form action="{{ route('admin.event-categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{ $categories->links() }}
        </div>
    </div>
@endsection
