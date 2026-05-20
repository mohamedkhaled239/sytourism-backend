{{-- resources/views/admin/events/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'تعديل الحدث')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">تعديل الحدث: {{ $event->title_ar }}</h5>
                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i> العودة للقائمة
                </a>
            </div>

            <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data"
                  data-ajax="true" 
                  data-confirm="هل أنت متأكد من حفظ التغييرات؟">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">العنوان بالإنجليزية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $event->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title_ar" class="form-label">العنوان بالعربية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title_ar') is-invalid @enderror"
                                   id="title_ar" name="title_ar" value="{{ old('title_ar', $event->title_ar) }}" required>
                            @error('title_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف بالإنجليزية <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="5" required>{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description_ar" class="form-label">الوصف بالعربية <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description_ar') is-invalid @enderror"
                                      id="description_ar" name="description_ar" rows="5" required>{{ old('description_ar', $event->description_ar) }}</textarea>
                            @error('description_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">التصنيف <span class="text-danger">*</span></label>
                            <select class="form-control @error('category_id') is-invalid @enderror"
                                    id="category_id" name="category_id" required>
                                <option value="">اختر التصنيف</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">المواقع <span class="text-danger">*</span></label>
                            <input type="text" id="location-search" class="form-control mb-2" placeholder="ابحث في المواقع...">
                            <div class="locations-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; border-radius: 0.375rem; padding: 10px;">
                                @foreach($locations as $location)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="locations[]"
                                               value="{{ $location->id }}" id="location_{{ $location->id }}"
                                               {{ $event->locations->contains($location->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="location_{{ $location->id }}">
                                            {{ $location->name_ar }}
                                            @if($location->address_ar)
                                                <small class="text-muted d-block">{{ $location->address_ar }}</small>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-text">يمكنك اختيار أي عدد من المواقع</div>
                            @error('locations')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">تاريخ البداية <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                   id="start_date" name="start_date" value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">تاريخ النهاية <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                   id="end_date" name="end_date" value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="main_image" class="form-label">الصورة الرئيسية</label>
                    <input type="file" class="form-control @error('main_image') is-invalid @enderror"
                           id="main_image" name="main_image" accept="image/*">
                    @error('main_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($event->main_image)
                        <div class="mt-2">
                            <img src="{{ Storage::url($event->main_image) }}" width="100" height="100"
                                 style="object-fit: cover; border-radius: 8px;">
                            <small class="text-muted d-block">الصورة الحالية</small>
                        </div>
                    @endif
                </div>

                <!-- قسم المنظمين -->
                <div class="mb-4">
                    <label class="form-label">المنظمون <span class="text-danger">*</span></label>
                    <div id="organizers-container">
                        @foreach($event->organizers as $index => $organizer)
                            <div class="organizer-item mb-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" name="organizers[{{ $index }}][name]" class="form-control"
                                               placeholder="الاسم (English)" value="{{ old('organizers.'.$index.'.name', $organizer->name) }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="organizers[{{ $index }}][name_ar]" class="form-control"
                                               placeholder="الاسم (عربي)" value="{{ old('organizers.'.$index.'.name_ar', $organizer->name_ar) }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="organizers[{{ $index }}][contact]" class="form-control"
                                               placeholder="معلومات الاتصال" value="{{ old('organizers.'.$index.'.contact', $organizer->contact) }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-organizer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($event->organizers->count() == 0)
                            <div class="organizer-item mb-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" name="organizers[0][name]" class="form-control"
                                               placeholder="الاسم (English)" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="organizers[0][name_ar]" class="form-control"
                                               placeholder="الاسم (عربي)" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="organizers[0][contact]" class="form-control"
                                               placeholder="معلومات الاتصال">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-organizer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="button" id="add-organizer" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fas fa-plus me-1"></i> إضافة منظم
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">حالة الحدث</label>
                    <select name="status" class="form-control" required>
                        <option value="not_started" {{ old('status', $event->status) == 'not_started' ? 'selected' : '' }}>لم يبدأ</option>
                        <option value="active" {{ old('status', $event->status) == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="ended" {{ old('status', $event->status) == 'ended' ? 'selected' : '' }}>منتهي</option>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published"
                               value="1" {{ old('is_published', $event->is_published) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_published">
                            نشر الحدث
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let organizerCount = {!! $event->organizers->count() > 0 ? $event->organizers->count() : 1 !!};

        // إضافة منظم جديد
        document.getElementById('add-organizer').addEventListener('click', function() {
            const container = document.getElementById('organizers-container');
            const newOrganizer = document.createElement('div');
            newOrganizer.className = 'organizer-item mb-2';
            newOrganizer.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="organizers[${organizerCount}][name]" class="form-control" placeholder="الاسم (English)" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="organizers[${organizerCount}][name_ar]" class="form-control" placeholder="الاسم (عربي)" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="organizers[${organizerCount}][contact]" class="form-control" placeholder="معلومات الاتصال">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-organizer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newOrganizer);
            organizerCount++;
        });

        // حذف منظم
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-organizer') || e.target.closest('.remove-organizer')) {
                const organizerItem = e.target.closest('.organizer-item');
                const container = document.getElementById('organizers-container');

                // التأكد من وجود منظم واحد على الأقل
                if (container.children.length > 1) {
                    organizerItem.remove();
                } else {
                    alert('يجب أن يكون هناك منظم واحد على الأقل');
                }
            }
        });

        // إضافة وظيفة البحث في المواقع
        const locationSearch = document.getElementById('location-search');
        const locationItems = document.querySelectorAll('.locations-checkboxes .form-check');

        if (locationSearch) {
            locationSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                locationItems.forEach(function(checkbox) {
                    const label = checkbox.querySelector('.form-check-label');
                    const text = label.textContent.toLowerCase();

                    if (text.includes(searchTerm)) {
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

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
@endpush
