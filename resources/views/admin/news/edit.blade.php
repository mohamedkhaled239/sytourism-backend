{{-- resources/views/admin/news/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'تعديل الخبر')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">تعديل الخبر: {{ $news->title_ar }}</h5>
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i> العودة للقائمة
                </a>
            </div>

            <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">العنوان بالإنجليزية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $news->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title_ar" class="form-label">العنوان بالعربية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title_ar') is-invalid @enderror"
                                   id="title_ar" name="title_ar" value="{{ old('title_ar', $news->title_ar) }}" required>
                            @error('title_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="content" class="form-label">المحتوى بالإنجليزية <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content" name="content" rows="8" required>{{ old('content', $news->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="content_ar" class="form-label">المحتوى بالعربية <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content_ar') is-invalid @enderror"
                                      id="content_ar" name="content_ar" rows="8" required>{{ old('content_ar', $news->content_ar) }}</textarea>
                            @error('content_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="main_image" class="form-label">الصورة الرئيسية</label>
                            <input type="file" class="form-control @error('main_image') is-invalid @enderror"
                                   id="main_image" name="main_image" accept="image/*">
                            @error('main_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($news->main_image)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($news->main_image) }}" width="100" height="100"
                                         style="object-fit: cover; border-radius: 8px;">
                                    <small class="text-muted d-block">الصورة الحالية</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="additional_images" class="form-label">صور إضافية</label>
                            <input type="file" class="form-control @error('additional_images.*') is-invalid @enderror"
                                   id="additional_images" name="additional_images[]" accept="image/*" multiple>
                            @error('additional_images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">يمكنك اختيار عدة صور</div>
                        </div>
                    </div>
                </div>

                @if($news->images && $news->images->count() > 0)
                <div class="mb-3">
                    <label class="form-label">الصور الإضافية الحالية</label>
                    <div class="row">
                        @foreach($news->images as $image)
                            <div class="col-md-2 mb-2">
                                <img src="{{ Storage::url($image->image_path) }}" width="100" height="100"
                                     style="object-fit: cover; border-radius: 8px;" class="img-thumbnail">
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">التصنيفات</label>
                    <input type="text" id="category-search" class="form-control mb-2" placeholder="ابحث في التصنيفات...">
                    <div class="categories-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; border-radius: 0.375rem; padding: 10px;">
                        @foreach($categories as $category)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]"
                                       value="{{ $category->id }}" id="category_{{ $category->id }}"
                                       {{ in_array($category->id, old('categories', $news->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
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
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published"
                               value="1" {{ old('is_published', $news->is_published) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_published">
                            نشر الخبر
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/category-search.js') }}"></script>
@endsection
