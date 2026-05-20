{{-- resources/views/admin/events/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'إضافة حدث جديد')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data"
                  data-ajax="true" 
                  data-confirm="هل أنت متأكد من إضافة هذا الحدث؟"
                  data-reset-on-success="true">
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
                            <label class="form-label">عنوان الحدث (عربي)</label>
                            <input type="text" name="title_ar" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">وصف الحدث (عربي)</label>
                            <textarea name="description_ar" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>

                    <div id="english" class="tab-pane">
                        <div class="mb-3">
                            <label class="form-label">Event Title (English)</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Event Description (English)</label>
                            <textarea name="description" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">تاريخ البداية</label>
                            <input type="datetime-local" name="start_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">تاريخ النهاية</label>
                            <input type="datetime-local" name="end_date" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">التصنيف</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">اختر التصنيف</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">المواقع <span class="text-danger">*</span></label>
                            <input type="text" id="location-search" class="form-control mb-2" placeholder="ابحث في المواقع...">
                            <div class="locations-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; border-radius: 0.375rem; padding: 10px;">
                                @foreach($locations as $location)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="locations[]"
                                               value="{{ $location->id }}" id="location_{{ $location->id }}">
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
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">الصورة الرئيسية</label>
                    <input type="file" name="main_image" class="form-control" accept="image/*" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">المنظمين</label>
                    <div id="organizers-container">
                        <div class="organizer-item mb-2">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="organizers[0][name]" class="form-control" placeholder="الاسم (English)" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="organizers[0][name_ar]" class="form-control" placeholder="الاسم (عربي)" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="organizers[0][contact]" class="form-control" placeholder="معلومات الاتصال">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm remove-organizer" style="display:none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-organizer" class="btn btn-secondary btn-sm mt-2">
                        <i class="fas fa-plus"></i> إضافة منظم آخر
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">حالة الحدث</label>
                    <select name="status" class="form-control" required>
                        <option value="not_started">لم يبدأ</option>
                        <option value="active">نشط</option>
                        <option value="ended">منتهي</option>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_published" id="is_published" checked>
                        <label class="form-check-label" for="is_published">
                            نشر الحدث
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">حفظ الحدث</button>
                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            let organizerCount = 1;

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
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
                container.appendChild(newOrganizer);
                organizerCount++;
                updateRemoveButtons();
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-organizer') || e.target.parentElement.classList.contains('remove-organizer')) {
                    const item = e.target.closest('.organizer-item');
                    item.remove();
                    updateRemoveButtons();
                }
            });

            function updateRemoveButtons() {
                const items = document.querySelectorAll('.organizer-item');
                items.forEach((item, index) => {
                    const removeBtn = item.querySelector('.remove-organizer');
                    if (items.length > 1) {
                        removeBtn.style.display = 'block';
                    } else {
                        removeBtn.style.display = 'none';
                    }
                });
            }

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
@endsection
