@extends('admin.layouts.app')

@section('title', 'تعديل التصنيف')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">تعديل التصنيف: {{ $category->name_ar }}</h5>
                <a href="{{ route('admin.categories.index', ['type' => $category->type]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i> العودة للقائمة
                </a>
            </div>

            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم بالإنجليزية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $category->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name_ar" class="form-label">الاسم بالعربية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                   id="name_ar" name="name_ar" value="{{ old('name_ar', $category->name_ar) }}" required>
                            @error('name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">نوع التصنيف <span class="text-danger">*</span></label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">اختر النوع</option>
                                <option value="news" {{ old('type', $category->type) == 'news' ? 'selected' : '' }}>أخبار</option>
                                <option value="events" {{ old('type', $category->type) == 'events' ? 'selected' : '' }}>أحداث</option>
                                <option value="investments" {{ old('type', $category->type) == 'investments' ? 'selected' : '' }}>استثمارات</option>
                                <option value="locations" {{ old('type', $category->type) == 'locations' ? 'selected' : '' }}>مواقع</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="color" class="form-label">اللون <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror"
                                       id="color" name="color" value="{{ old('color', $category->color) }}" required>
                                <input type="text" class="form-control" id="color-text" value="{{ old('color', $category->color) }}" readonly>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف بالإنجليزية</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description_ar" class="form-label">الوصف بالعربية</label>
                            <textarea class="form-control @error('description_ar') is-invalid @enderror"
                                      id="description_ar" name="description_ar" rows="3">{{ old('description_ar', $category->description_ar) }}</textarea>
                            @error('description_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            التصنيف نشط
                        </label>
                    </div>
                </div>

                <!-- معاينة التصنيف -->
                <div class="mb-3">
                    <label class="form-label">معاينة التصنيف</label>
                    <div class="p-3 border rounded">
                        <span id="category-preview" class="badge" style="background-color: {{ $category->color }}; color: white;">
                            {{ $category->name_ar }}
                        </span>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorInput = document.getElementById('color');
        const colorText = document.getElementById('color-text');
        const nameArInput = document.getElementById('name_ar');
        const preview = document.getElementById('category-preview');

        // تحديث النص عند تغيير اللون
        colorInput.addEventListener('input', function() {
            colorText.value = this.value;
            updatePreview();
        });

        // تحديث المعاينة عند تغيير الاسم
        nameArInput.addEventListener('input', function() {
            updatePreview();
        });

        function updatePreview() {
            const color = colorInput.value;
            const name = nameArInput.value || 'معاينة التصنيف';

            preview.style.backgroundColor = color;
            preview.textContent = name;
        }
    });
</script>
@endpush
