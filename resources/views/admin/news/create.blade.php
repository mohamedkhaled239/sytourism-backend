{{-- resources/views/admin/news/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'إضافة خبر جديد')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#arabic">العربية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#english">English</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="arabic" class="tab-pane active">
                        <div class="mb-3">
                            <label class="form-label">العنوان (عربي)</label>
                            <input type="text" name="title_ar" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">المحتوى (عربي)</label>
                            <textarea name="content_ar" class="form-control" rows="10" required></textarea>
                        </div>
                    </div>

                    <div id="english" class="tab-pane">
                        <div class="mb-3">
                            <label class="form-label">Title (English)</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Content (English)</label>
                            <textarea name="content" class="form-control" rows="10" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">الصورة الرئيسية</label>
                    <input type="file" name="main_image" class="form-control" accept="image/*" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">صور إضافية (اختياري - حد أقصى 4)</label>
                    <input type="file" name="additional_images[]" class="form-control" accept="image/*" multiple>
                </div>

                <div class="mb-3">
                    <label class="form-label">التصنيفات</label>
                    <input type="text" id="category-search" class="form-control mb-2" placeholder="ابحث في التصنيفات...">
                    <div class="categories-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; border-radius: 0.375rem; padding: 10px;">
                        @foreach($categories as $category)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]"
                                       value="{{ $category->id }}" id="category_{{ $category->id }}">
                                <label class="form-check-label" for="category_{{ $category->id }}">
                                    <span class="badge" style="background-color: {{ $category->color }}; color: white;">{{ $category->name_ar }}</span>
                                    @if($category->description_ar)
                                        <small class="text-muted d-block">{{ $category->description_ar }}</small>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text">يمكنك اختيار أي عدد من التصنيفات</div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_published" id="is_published" checked>
                        <label class="form-check-label" for="is_published">
                            نشر الخبر
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">حفظ الخبر</button>
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/category-search.js') }}"></script>
@endsection
