@extends('admin.layouts.app')

@section('title', 'تعديل الموقع')

@section('content')
    @php
        $adminGovernorateId = auth()->guard('admin')->user()->governorate_id;
        $citiesByGovernorate = $cities->groupBy('governorate_id')->map(function ($items) {
            return $items->map(fn ($city) => ['id' => $city->id, 'name_ar' => $city->name_ar])->values();
        });
    @endphp

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">تعديل الموقع: {{ $location->name_ar }}</h5>
                <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">العودة</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.locations.update', $location->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">الاسم بالإنجليزية</label><input type="text" name="name" class="form-control" value="{{ old('name', $location->name) }}" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">الاسم بالعربية</label><input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $location->name_ar) }}" required></div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">المحافظة</label>
                        <select name="governorate_id" id="governorate_id" class="form-select" {{ $adminGovernorateId ? 'disabled' : '' }}>
                            <option value="">اختر المحافظة</option>
                            @foreach($governorates as $governorate)
                                <option value="{{ $governorate->id }}" {{ old('governorate_id', $location->governorate_id ?? $adminGovernorateId) == $governorate->id ? 'selected' : '' }}>{{ $governorate->name_ar }}</option>
                            @endforeach
                        </select>
                        @if($adminGovernorateId)
                            <input type="hidden" name="governorate_id" value="{{ $adminGovernorateId }}">
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">المدينة</label>
                        <select name="city_id" id="city_id" class="form-select">
                            <option value="">اختر المدينة</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نوع السياحة</label>
                        <select name="tourism_type_id" class="form-select">
                            <option value="">اختر النوع</option>
                            @foreach($tourismTypes as $type)
                                <option value="{{ $type->id }}" {{ old('tourism_type_id', $location->tourism_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">أنواع الموقع</label>
                    <div class="row">
                        @foreach($locationTypes as $type)
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="location_types[]" value="{{ $type->id }}" id="location_type_{{ $type->id }}" {{ $location->locationTypes->contains($type->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="location_type_{{ $type->id }}">{{ $type->name_ar }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">التصنيفات</label>
                    <div class="row">
                        @foreach($categories as $category)
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" id="category_{{ $category->id }}" {{ $location->categories->contains($category->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category_{{ $category->id }}">{{ $category->name_ar }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">الهاتف</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $location->phone) }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">البريد الإلكتروني</label><input type="email" name="email" class="form-control" value="{{ old('email', $location->email) }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">الموقع الإلكتروني</label><input type="url" name="website" class="form-control" value="{{ old('website', $location->website) }}"></div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">خط العرض</label><input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude', $location->latitude) }}"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">خط الطول</label><input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude', $location->longitude) }}"></div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">العنوان بالإنجليزية</label><textarea name="address" class="form-control" rows="3">{{ old('address', $location->address) }}</textarea></div>
                    <div class="col-md-6 mb-3"><label class="form-label">العنوان بالعربية</label><textarea name="address_ar" class="form-control" rows="3">{{ old('address_ar', $location->address_ar) }}</textarea></div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">الوصف بالإنجليزية</label><textarea name="description" class="form-control" rows="4">{{ old('description', $location->description) }}</textarea></div>
                    <div class="col-md-6 mb-3"><label class="form-label">الوصف بالعربية</label><textarea name="description_ar" class="form-control" rows="4">{{ old('description_ar', $location->description_ar) }}</textarea></div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">المميزات بالإنجليزية</label><textarea name="features" class="form-control" rows="3">{{ old('features', $location->features) }}</textarea></div>
                    <div class="col-md-6 mb-3"><label class="form-label">المميزات بالعربية</label><textarea name="features_ar" class="form-control" rows="3">{{ old('features_ar', $location->features_ar) }}</textarea></div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">التقييم</label><input type="number" step="0.1" min="0" max="5" name="rating" class="form-control" value="{{ old('rating', $location->rating) }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">وصف التقييم بالإنجليزية</label><input type="text" name="rating_description" class="form-control" value="{{ old('rating_description', $location->rating_description) }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">وصف التقييم بالعربية</label><input type="text" name="rating_description_ar" class="form-control" value="{{ old('rating_description_ar', $location->rating_description_ar) }}"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">الصورة الرئيسية</label>
                    @if($location->main_image)
                        <div class="mb-2">
                            <img src="{{ $location->main_image_url }}" alt="الصورة الرئيسية" class="img-thumbnail" style="max-width: 220px;">
                        </div>
                    @endif
                    <input type="file" name="main_image" class="form-control" accept="image/*">
                </div>

                @if($location->images->count())
                    <div class="mb-3">
                        <label class="form-label">الصور الحالية</label>
                        <div class="row">
                            @foreach($location->images as $image)
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        <img src="{{ $image->image_url }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="image">
                                        <div class="card-body">
                                            <div class="small mb-2">{{ $image->caption_ar ?: $image->caption ?: 'بدون وصف' }}</div>
                                            <form action="{{ route('admin.locations.delete-image', [$location->id, $image->id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('حذف هذه الصورة؟')">حذف</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">إضافة صور جديدة</label>
                    <div id="images-container">
                        <div class="row g-2 image-upload-item mb-2">
                            <div class="col-md-5"><input type="file" name="images[]" class="form-control" accept="image/*"></div>
                            <div class="col-md-3"><input type="text" name="image_captions[]" class="form-control" placeholder="وصف الصورة بالإنجليزية"></div>
                            <div class="col-md-3"><input type="text" name="image_captions_ar[]" class="form-control" placeholder="وصف الصورة بالعربية"></div>
                            <div class="col-md-1"><button type="button" class="btn btn-outline-danger remove-image-btn d-none"><i class="fas fa-trash"></i></button></div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-image-btn">إضافة صورة أخرى</button>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">الموقع نشط</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const governorateSelect = document.getElementById('governorate_id');
            const citySelect = document.getElementById('city_id');
            const citiesByGovernorate = @json($citiesByGovernorate);
            const currentCityId = '{{ old('city_id', $location->city_id) }}';

            function updateCities() {
                const governorateId = governorateSelect ? governorateSelect.value : '{{ $adminGovernorateId }}';
                const cities = citiesByGovernorate[governorateId] || [];
                citySelect.innerHTML = '<option value="">اختر المدينة</option>';
                cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.id;
                    option.textContent = city.name_ar;
                    if (currentCityId && currentCityId == city.id) option.selected = true;
                    citySelect.appendChild(option);
                });
            }

            if (governorateSelect) governorateSelect.addEventListener('change', updateCities);
            updateCities();

            document.getElementById('add-image-btn').addEventListener('click', function() {
                const container = document.getElementById('images-container');
                const item = document.createElement('div');
                item.className = 'row g-2 image-upload-item mb-2';
                item.innerHTML = '<div class="col-md-5"><input type="file" name="images[]" class="form-control" accept="image/*"></div><div class="col-md-3"><input type="text" name="image_captions[]" class="form-control" placeholder="وصف الصورة بالإنجليزية"></div><div class="col-md-3"><input type="text" name="image_captions_ar[]" class="form-control" placeholder="وصف الصورة بالعربية"></div><div class="col-md-1"><button type="button" class="btn btn-outline-danger remove-image-btn"><i class="fas fa-trash"></i></button></div>';
                container.appendChild(item);
            });

            document.getElementById('images-container').addEventListener('click', function(event) {
                if (event.target.closest('.remove-image-btn')) {
                    event.target.closest('.image-upload-item').remove();
                }
            });
        });
    </script>
@endpush
