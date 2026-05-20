@extends('admin.layouts.app')

@section('title', 'تعديل مدينة')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">تعديل المدينة: {{ $city->name_ar }}</h5>
                <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary">العودة</a>
            </div>

            <form action="{{ route('admin.cities.update', $city->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المحافظة</label>
                        <select name="governorate_id" class="form-select @error('governorate_id') is-invalid @enderror" required>
                            <option value="">اختر المحافظة</option>
                            @foreach($governorates as $governorate)
                                <option value="{{ $governorate->id }}" {{ old('governorate_id', $city->governorate_id) == $governorate->id ? 'selected' : '' }}>{{ $governorate->name_ar }}</option>
                            @endforeach
                        </select>
                        @error('governorate_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">اسم المدينة بالعربية</label>
                        <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $city->name_ar) }}" required>
                        @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">اسم المدينة بالإنجليزية</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $city->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $city->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">المدينة نشطة</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
@endsection
