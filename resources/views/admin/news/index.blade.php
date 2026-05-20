{{-- resources/views/admin/news/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'الأخبار')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">قائمة الأخبار</h5>
                <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> إضافة خبر جديد
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
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($news as $newsItem)
                        <tr>
                            <td>{{ $newsItem->id }}</td>
                            <td>
                                <img src="{{ Storage::url($newsItem->main_image) }}" width="50" height="50" style="object-fit: cover; border-radius: 8px;">
                            </td>
                            <td>{{ $newsItem->title }}</td>
                            <td>{{ $newsItem->title_ar }}</td>
                            <td>
                                @if($newsItem->is_published)
                                    <span class="badge bg-success">منشور</span>
                                @else
                                    <span class="badge bg-warning">مسودة</span>
                                @endif
                            </td>
                            <td>{{ $newsItem->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.news.edit', $newsItem->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.news.destroy', $newsItem->id) }}" method="POST" class="d-inline">
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

            {{ $news->links() }}
        </div>
    </div>
@endsection
