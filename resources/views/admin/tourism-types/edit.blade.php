@extends('admin.layouts.app')

@section('title', 'تعديل نوع السياحة')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">تعديل نوع السياحة: {{ $tourismType->name_ar }}</h5>
                <a href="{{ route('admin.tourism-types.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i> العودة للقائمة
                </a>
            </div>

            <form action="{{ route('admin.tourism-types.update', $tourismType->id) }}" method="POST"
                  data-ajax="true" 
                  data-confirm="هل أنت متأكد من حفظ التغييرات؟">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم بالإنجليزية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $tourismType->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name_ar" class="form-label">الاسم بالعربية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                   id="name_ar" name="name_ar" value="{{ old('name_ar', $tourismType->name_ar) }}" required>
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
                                       id="color" name="color" value="{{ old('color', $tourismType->color) }}" required>
                                <input type="text" class="form-control" id="color-text" value="{{ old('color', $tourismType->color) }}" readonly>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="icon" class="form-label">الأيقونة <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i id="icon-preview" class="{{ $tourismType->icon }}"></i>
                                </span>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                       id="icon" name="icon" value="{{ old('icon', $tourismType->icon) }}" required
                                       placeholder="fas fa-landmark">
                            </div>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">استخدم أيقونات Font Awesome مثل: fas fa-landmark, fas fa-mosque, fas fa-tree</div>
                        </div>
                    </div>
                </div>

                <!-- أيقونات شائعة -->
                <div class="mb-3">
                    <label class="form-label">أيقونات شائعة</label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-landmark">
                            <i class="fas fa-landmark"></i> معالم
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-mosque">
                            <i class="fas fa-mosque"></i> مساجد
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-tree">
                            <i class="fas fa-tree"></i> طبيعة
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-mountain">
                            <i class="fas fa-mountain"></i> جبال
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-hospital">
                            <i class="fas fa-hospital"></i> علاج
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-briefcase">
                            <i class="fas fa-briefcase"></i> أعمال
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-monument">
                            <i class="fas fa-monument"></i> آثار
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف بالإنجليزية</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $tourismType->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description_ar" class="form-label">الوصف بالعربية</label>
                            <textarea class="form-control @error('description_ar') is-invalid @enderror"
                                      id="description_ar" name="description_ar" rows="3">{{ old('description_ar', $tourismType->description_ar) }}</textarea>
                            @error('description_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $tourismType->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            نوع السياحة نشط
                        </label>
                    </div>
                </div>

                <!-- معاينة النوع -->
                <div class="mb-3">
                    <label class="form-label">معاينة النوع</label>
                    <div class="p-3 border rounded">
                        <div class="d-flex align-items-center">
                            <i id="type-preview-icon" class="{{ $tourismType->icon }}" style="color: {{ $tourismType->color }}; font-size: 1.5em;"></i>
                            <span id="type-preview-text" class="ms-2 fw-bold">{{ $tourismType->name_ar }}</span>
                        </div>
                    </div>
                </div>

                <!-- معلومات إضافية -->
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>معلومات إضافية</h6>
                    <p class="mb-1"><strong>عدد المواقع المرتبطة:</strong> {{ $tourismType->locations()->count() }}</p>
                    <p class="mb-0"><strong>تاريخ الإنشاء:</strong> {{ $tourismType->created_at->format('Y-m-d H:i') }}</p>
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
        const iconInput = document.getElementById('icon');
        const iconPreview = document.getElementById('icon-preview');
        const nameArInput = document.getElementById('name_ar');
        const typePreviewIcon = document.getElementById('type-preview-icon');
        const typePreviewText = document.getElementById('type-preview-text');

        // تحديث النص عند تغيير اللون
        colorInput.addEventListener('input', function() {
            colorText.value = this.value;
            updatePreview();
        });

        // تحديث الأيقونة عند تغيير النص
        iconInput.addEventListener('input', function() {
            updateIconPreview();
            updatePreview();
        });

        // تحديث المعاينة عند تغيير الاسم
        nameArInput.addEventListener('input', function() {
            updatePreview();
        });

        // أزرار الأيقونات الشائعة
        document.querySelectorAll('.icon-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const icon = this.dataset.icon;
                iconInput.value = icon;
                updateIconPreview();
                updatePreview();
            });
        });

        function updateIconPreview() {
            const iconClass = iconInput.value || 'fas fa-landmark';
            iconPreview.className = iconClass;
        }

        function updatePreview() {
            const color = colorInput.value;
            const icon = iconInput.value || 'fas fa-landmark';
            const name = nameArInput.value || 'معاينة نوع السياحة';

            typePreviewIcon.className = icon;
            typePreviewIcon.style.color = color;
            typePreviewText.textContent = name;
        }
    });
</script>
@endpush
