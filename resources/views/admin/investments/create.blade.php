{{-- resources/views/admin/investments/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'إضافة استثمار جديد')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.investments.store') }}" method="POST" enctype="multipart/form-data"
                  data-ajax="true" 
                  data-confirm="هل أنت متأكد من إضافة هذا الاستثمار؟"
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
                            <label class="form-label">عنوان الاستثمار (عربي)</label>
                            <input type="text" name="title_ar" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">وصف الاستثمار (عربي)</label>
                            <textarea name="description_ar" class="form-control" rows="8" required></textarea>
                        </div>
                    </div>

                    <div id="english" class="tab-pane">
                        <div class="mb-3">
                            <label class="form-label">Investment Title (English)</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Investment Description (English)</label>
                            <textarea name="description" class="form-control" rows="8" required></textarea>
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
                    <label class="form-label">الصورة الرئيسية</label>
                    <input type="file" name="main_image" class="form-control" accept="image/*" required>
                </div>

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
                    <div class="form-text">يمكنك اختيار أي عدد من المواقع (على الأقل موقع واحد)</div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_published" id="is_published" checked>
                        <label class="form-check-label" for="is_published">
                            نشر الاستثمار
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">حفظ الاستثمار</button>
                <a href="{{ route('admin.investments.index') }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // إضافة وظائف للمواقع
        const locationCheckboxes = document.querySelectorAll('input[name="locations[]"]');
        const form = document.querySelector('form');

        // التحقق من اختيار موقع واحد على الأقل عند الإرسال
        form.addEventListener('submit', function(e) {
            const checkedLocations = document.querySelectorAll('input[name="locations[]"]:checked');
            if (checkedLocations.length === 0) {
                e.preventDefault();
                alert('يجب اختيار موقع واحد على الأقل');
                return false;
            }


        });

        // إضافة زر "تحديد الكل" و "إلغاء تحديد الكل"
        const locationsContainer = document.querySelector('.locations-checkboxes');
        if (locationsContainer) {
            const controlsDiv = document.createElement('div');
            controlsDiv.className = 'mb-2 pb-2 border-bottom';
            controlsDiv.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-primary me-2" id="select-all-locations">
                    تحديد الكل
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect-all-locations">
                    إلغاء تحديد الكل
                </button>
            `;
            locationsContainer.insertBefore(controlsDiv, locationsContainer.firstChild);

            // وظائف الأزرار
            document.getElementById('select-all-locations').addEventListener('click', function() {
                locationCheckboxes.forEach(checkbox => checkbox.checked = true);
            });

            document.getElementById('deselect-all-locations').addEventListener('click', function() {
                locationCheckboxes.forEach(checkbox => checkbox.checked = false);
            });
        }

        // إضافة وظيفة البحث في المواقع
        const locationSearch = document.getElementById('location-search');
        const locationItems = document.querySelectorAll('.form-check');

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

        // إضافة وظيفة البحث في التصنيفات
        const categorySearch = document.getElementById('category-search');
        const categoryItems = document.querySelectorAll('.categories-checkboxes .form-check');

        if (categorySearch) {
            categorySearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                categoryItems.forEach(function(checkbox) {
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
