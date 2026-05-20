{{-- resources/views/admin/investments/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'تعديل الاستثمار')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">تعديل الاستثمار: {{ $investment->title_ar }}</h5>
                <a href="{{ route('admin.investments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i> العودة للقائمة
                </a>
            </div>

            <form action="{{ route('admin.investments.update', $investment->id) }}" method="POST" enctype="multipart/form-data"
                  data-ajax="true" 
                  data-confirm="هل أنت متأكد من حفظ التغييرات؟">
                @csrf
                @method('PUT')

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
                            <label class="form-label">عنوان الاستثمار (عربي)</label>
                            <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror" 
                                   value="{{ old('title_ar', $investment->title_ar) }}" required>
                            @error('title_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">وصف الاستثمار (عربي)</label>
                            <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror" 
                                      rows="8" required>{{ old('description_ar', $investment->description_ar) }}</textarea>
                            @error('description_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div id="english" class="tab-pane">
                        <div class="mb-3">
                            <label class="form-label">Investment Title (English)</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $investment->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Investment Description (English)</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="8" required>{{ old('description', $investment->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">التصنيفات</label>
                    <input type="text" id="category-search" class="form-control mb-2" placeholder="ابحث في التصنيفات...">
                    <div class="categories-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; border-radius: 0.375rem; padding: 10px;">
                        @foreach($categories as $category)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" 
                                       value="{{ $category->id }}" id="category_{{ $category->id }}"
                                       {{ in_array($category->id, old('categories', $investment->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
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
                    <label class="form-label">المواقع</label>
                    <input type="text" id="location-search" class="form-control mb-2" placeholder="ابحث في المواقع...">
                    <div class="locations-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; border-radius: 0.375rem; padding: 10px;">
                        @foreach($locations as $location)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="locations[]" 
                                       value="{{ $location->id }}" id="location_{{ $location->id }}"
                                       {{ in_array($location->id, old('locations', $investment->locations->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label" for="location_{{ $location->id }}">
                                    {{ $location->name_ar }}
                                    @if($location->address_ar)
                                        <small class="text-muted d-block">{{ $location->address_ar }}</small>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text">يمكنك اختيار أي عدد من المواقع (على الأقل موقع واحد)</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">الصورة الرئيسية</label>
                    <input type="file" name="main_image" class="form-control @error('main_image') is-invalid @enderror" accept="image/*">
                    @error('main_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($investment->main_image)
                        <div class="mt-2">
                            <img src="{{ Storage::url($investment->main_image) }}" width="100" height="100" 
                                 style="object-fit: cover; border-radius: 8px;" class="img-thumbnail">
                            <small class="text-muted d-block">الصورة الحالية</small>
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_published" id="is_published" 
                               {{ old('is_published', $investment->is_published) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_published">
                            نشر الاستثمار
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                <a href="{{ route('admin.investments.index') }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Category search functionality
        const categorySearch = document.getElementById('category-search');
        const categoryCheckboxes = document.querySelectorAll('.categories-checkboxes .form-check');
        
        if (categorySearch) {
            categorySearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                categoryCheckboxes.forEach(function(checkbox) {
                    const label = checkbox.querySelector('label').textContent.toLowerCase();
                    if (label.includes(searchTerm)) {
                        checkbox.style.display = 'block';
                    } else {
                        checkbox.style.display = 'none';
                    }
                });
            });
        }
        
        // Location search functionality
        const locationSearch = document.getElementById('location-search');
        const locationCheckboxes = document.querySelectorAll('.locations-checkboxes .form-check');
        
        if (locationSearch) {
            locationSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                locationCheckboxes.forEach(function(checkbox) {
                    const label = checkbox.querySelector('label').textContent.toLowerCase();
                    if (label.includes(searchTerm)) {
                        checkbox.style.display = 'block';
                    } else {
                        checkbox.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush
