@extends('admin.layouts.app')

@section('title', 'إضافة نوع موقع جديد')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">إضافة نوع موقع جديد</h5>
                <a href="{{ route('admin.location-types.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i> العودة للقائمة
                </a>
            </div>

            <form action="{{ route('admin.location-types.store') }}" method="POST"
                  data-ajax="true"
                  data-confirm="هل أنت متأكد من إضافة نوع الموقع؟"
                  data-reset-on-success="true">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم بالإنجليزية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name_ar" class="form-label">الاسم بالعربية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                   id="name_ar" name="name_ar" value="{{ old('name_ar') }}" required>
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
                                       id="color" name="color" value="{{ old('color', '#3E5828') }}" required>
                                <input type="text" class="form-control" id="color-text" value="{{ old('color', '#3E5828') }}" readonly>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="icon" class="form-label">الأيقونة (Font Awesome)</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i id="icon-preview" class="fas fa-map-marker-alt"></i>
                                </span>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                       id="icon" name="icon" value="{{ old('icon', 'fas fa-map-marker-alt') }}"
                                       placeholder="fas fa-hotel">
                            </div>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">اختياري - أيقونات Font Awesome مثل: fas fa-hotel, fas fa-utensils</div>
                        </div>
                    </div>
                </div>

                {{-- ===== اختيار صورة دبوس الخريطة ===== --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-map-pin me-1 text-danger"></i>
                        صورة دبوس الخريطة (Pin Image)
                    </label>
                    <input type="hidden" name="pin_image" id="pin_image" value="{{ old('pin_image') }}">

                    @if(count($availablePins) > 0)
                        <div class="pin-selector-grid" id="pin-grid">
                            @foreach($availablePins as $pin)
                                <div class="pin-item {{ old('pin_image') === $pin ? 'selected' : '' }}"
                                     data-pin="{{ $pin }}"
                                     title="{{ pathinfo($pin, PATHINFO_FILENAME) }}">
                                    <img src="{{ asset('images/location-type-pins/' . $pin) }}"
                                         alt="{{ pathinfo($pin, PATHINFO_FILENAME) }}"
                                         class="pin-img">
                                    <small class="pin-name">{{ Str::limit(pathinfo($pin, PATHINFO_FILENAME), 10) }}</small>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text mt-1">اضغط على الدبوس المطلوب لاختياره</div>
                    @else
                        <div class="alert alert-warning">
                            لا توجد صور دبابيس في المجلد <code>public/images/location-type-pins/</code>
                        </div>
                    @endif

                    {{-- معاينة الدبوس المختار --}}
                    <div id="pin-preview-box" class="mt-2 {{ old('pin_image') ? '' : 'd-none' }}">
                        <div class="d-flex align-items-center gap-2">
                            <img id="pin-preview-img" src="{{ old('pin_image') ? asset('images/location-type-pins/' . old('pin_image')) : '' }}"
                                 style="width:48px;height:48px;object-fit:contain;" alt="دبوس مختار">
                            <div>
                                <strong id="pin-preview-name">{{ old('pin_image') ? pathinfo(old('pin_image'), PATHINFO_FILENAME) : '' }}</strong>
                                <br>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0" id="clear-pin">
                                    <i class="fas fa-times"></i> إلغاء الاختيار
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أيقونات شائعة -->
                <div class="mb-3">
                    <label class="form-label">أيقونات شائعة</label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-hotel">
                            <i class="fas fa-hotel"></i> فندق
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-utensils">
                            <i class="fas fa-utensils"></i> مطعم
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-university">
                            <i class="fas fa-university"></i> متحف
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-landmark">
                            <i class="fas fa-landmark"></i> معلم تاريخي
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-tree">
                            <i class="fas fa-tree"></i> حديقة
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-water">
                            <i class="fas fa-water"></i> شاطئ
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-shopping-cart">
                            <i class="fas fa-shopping-cart"></i> تسوق
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-gamepad">
                            <i class="fas fa-gamepad"></i> ترفيه
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-mosque">
                            <i class="fas fa-mosque"></i> موقع ديني
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm icon-btn" data-icon="fas fa-monument">
                            <i class="fas fa-monument"></i> موقع أثري
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف بالإنجليزية</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description_ar" class="form-label">الوصف بالعربية</label>
                            <textarea class="form-control @error('description_ar') is-invalid @enderror"
                                      id="description_ar" name="description_ar" rows="3">{{ old('description_ar') }}</textarea>
                            @error('description_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            نوع الموقع نشط
                        </label>
                    </div>
                </div>

                <!-- معاينة النوع -->
                <div class="mb-3">
                    <label class="form-label">معاينة النوع</label>
                    <div class="p-3 border rounded">
                        <div class="d-flex align-items-center">
                            <i id="type-preview-icon" class="fas fa-map-marker-alt" style="color: #3E5828; font-size: 1.5em;"></i>
                            <span id="type-preview-text" class="ms-2 fw-bold">معاينة نوع الموقع</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> حفظ نوع الموقع
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
.pin-selector-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 8px;
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 12px;
    background: #f8f9fa;
}
.pin-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 8px 4px;
    border-radius: 8px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s;
    background: #fff;
}
.pin-item:hover {
    border-color: #0d6efd;
    background: #e7f1ff;
}
.pin-item.selected {
    border-color: #0d6efd;
    background: #cfe2ff;
    box-shadow: 0 0 0 3px rgba(13,110,253,0.25);
}
.pin-img {
    width: 40px;
    height: 40px;
    object-fit: contain;
}
.pin-name {
    font-size: 10px;
    text-align: center;
    color: #555;
    margin-top: 4px;
    word-break: break-all;
}
</style>
@endpush

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
        const pinImageInput = document.getElementById('pin_image');
        const pinPreviewBox = document.getElementById('pin-preview-box');
        const pinPreviewImg = document.getElementById('pin-preview-img');
        const pinPreviewName = document.getElementById('pin-preview-name');
        const clearPinBtn = document.getElementById('clear-pin');

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

        // اختيار صورة الدبوس
        document.querySelectorAll('.pin-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.pin-item').forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');
                const pin = this.dataset.pin;
                pinImageInput.value = pin;
                pinPreviewImg.src = '/images/location-type-pins/' + pin;
                pinPreviewName.textContent = pin.replace('.png', '');
                pinPreviewBox.classList.remove('d-none');
            });
        });

        // إلغاء اختيار الدبوس
        if (clearPinBtn) {
            clearPinBtn.addEventListener('click', function() {
                pinImageInput.value = '';
                pinPreviewBox.classList.add('d-none');
                document.querySelectorAll('.pin-item').forEach(i => i.classList.remove('selected'));
            });
        }

        function updateIconPreview() {
            const iconClass = iconInput.value || 'fas fa-map-marker-alt';
            iconPreview.className = iconClass;
        }

        function updatePreview() {
            const color = colorInput.value;
            const icon = iconInput.value || 'fas fa-map-marker-alt';
            const name = nameArInput.value || 'معاينة نوع الموقع';

            typePreviewIcon.className = icon;
            typePreviewIcon.style.color = color;
            typePreviewText.textContent = name;
        }

        // تحديث المعاينة عند تحميل الصفحة
        updateIconPreview();
        updatePreview();
    });
</script>
@endpush
