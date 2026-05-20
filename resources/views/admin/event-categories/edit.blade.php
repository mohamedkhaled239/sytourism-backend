{{-- resources/views/admin/event-categories/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'تعديل تصنيف الأحداث')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">تعديل التصنيف: {{ $category->name_ar }}</h5>
                <a href="{{ route('admin.event-categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i> العودة للقائمة
                </a>
            </div>

            <form action="{{ route('admin.event-categories.update', $category->id) }}" method="POST">
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
                            <label for="color" class="form-label">اللون <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color" name="color" value="{{ old('color', $category->color) }}" required>
                                <input type="text" class="form-control" id="color_text" 
                                       value="{{ old('color', $category->color) }}" readonly>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">اختر لوناً مميزاً لهذا التصنيف</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">معاينة اللون</label>
                            <div class="d-flex align-items-center">
                                <span class="badge me-2" id="color_preview" 
                                      style="background-color: {{ old('color', $category->color) }}; padding: 10px 20px;">
                                    {{ old('name_ar', $category->name_ar) }}
                                </span>
                                <small class="text-muted">هكذا سيظهر التصنيف</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">الوصف (اختياري)</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description_ar" class="form-label">الوصف بالعربية (اختياري)</label>
                    <textarea class="form-control @error('description_ar') is-invalid @enderror" 
                              id="description_ar" name="description_ar" rows="3">{{ old('description_ar', $category->description_ar) }}</textarea>
                    @error('description_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
        const colorText = document.getElementById('color_text');
        const colorPreview = document.getElementById('color_preview');
        const nameArInput = document.getElementById('name_ar');
        
        // تحديث معاينة اللون
        function updateColorPreview() {
            const color = colorInput.value;
            const nameAr = nameArInput.value || 'اسم التصنيف';
            
            colorText.value = color;
            colorPreview.style.backgroundColor = color;
            colorPreview.textContent = nameAr;
        }
        
        colorInput.addEventListener('change', updateColorPreview);
        nameArInput.addEventListener('input', updateColorPreview);
        
        // تحديث أولي
        updateColorPreview();
    });
</script>
@endpush
