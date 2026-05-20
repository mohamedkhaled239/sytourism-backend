@extends('admin.layouts.app')

@section('title', 'تعديل محافظة')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">تعديل المحافظة: {{ $governorate->name_ar }}</h5>
                <a href="{{ route('admin.governorates.index') }}" class="btn btn-secondary">العودة</a>
            </div>

            <form action="{{ route('admin.governorates.update', $governorate->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الاسم بالإنجليزية</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $governorate->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الاسم بالعربية</label>
                        <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $governorate->name_ar) }}" required>
                        @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">الكود</label>
                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $governorate->code) }}" required maxlength="10">
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف بالإنجليزية</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $governorate->description) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف بالعربية</label>
                        <textarea name="description_ar" class="form-control" rows="3">{{ old('description_ar', $governorate->description_ar) }}</textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $governorate->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">المحافظة نشطة</label>
                    </div>
                </div>

                <div class="alert alert-info">
                    <div>عدد المدن: {{ $governorate->cities()->count() }}</div>
                    <div>عدد المواقع: {{ $governorate->locations()->count() }}</div>
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
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '');
        });
    </script>
@endpush
