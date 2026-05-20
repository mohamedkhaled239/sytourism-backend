@extends('admin.layouts.app')

@section('title', 'تعديل الحساب الإداري')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">تعديل الحساب: {{ $admin->name }}</h5>
                <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">العودة</a>
            </div>

            <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الاسم</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $admin->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $admin->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">كلمة المرور الجديدة</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_super_admin" name="is_super_admin" value="1" {{ old('is_super_admin', $admin->is_super_admin) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_super_admin">مشرف عام</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نوع الحساب</label>
                        <select name="account_type" id="account_type" class="form-select @error('account_type') is-invalid @enderror">
                            <option value="admin" {{ old('account_type', $admin->account_type) == 'admin' ? 'selected' : '' }}>مشرف</option>
                            <option value="data_entry_tourism_establishments" {{ old('account_type', $admin->account_type) == 'data_entry_tourism_establishments' ? 'selected' : '' }}>مدخل منشآت سياحية</option>
                            <option value="data_entry_attractions_routes" {{ old('account_type', $admin->account_type) == 'data_entry_attractions_routes' ? 'selected' : '' }}>مدخل مواقع الجذب والمسارات</option>
                        </select>
                        @error('account_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3" id="governorate-wrapper">
                        <label class="form-label">المحافظة</label>
                        <select name="governorate_id" class="form-select @error('governorate_id') is-invalid @enderror">
                            <option value="">اختر المحافظة</option>
                            @foreach($governorates as $governorate)
                                <option value="{{ $governorate->id }}" {{ old('governorate_id', $admin->governorate_id) == $governorate->id ? 'selected' : '' }}>{{ $governorate->name_ar }}</option>
                            @endforeach
                        </select>
                        @error('governorate_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div id="permissions-wrapper">
                    <label class="form-label">الصلاحيات</label>
                    <div class="row">
                        @php
                            $permissions = [
                                'users' => 'المستخدمين',
                                'news' => 'الأخبار',
                                'events' => 'الأحداث',
                                'investments' => 'الاستثمارات',
                                'locations' => 'المواقع',
                                'map' => 'الخريطة',
                                'categories' => 'التصنيفات',
                                'settings' => 'الإعدادات',
                            ];
                            $selectedPermissions = old('permissions', $admin->permissions ?? []);
                        @endphp
                        @foreach($permissions as $key => $label)
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="perm_{{ $key }}" name="permissions[]" value="{{ $key }}" {{ is_array($selectedPermissions) && in_array($key, $selectedPermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $key }}">{{ $label }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isSuperAdmin = document.getElementById('is_super_admin');
            const accountType = document.getElementById('account_type');
            const governorateWrapper = document.getElementById('governorate-wrapper');
            const permissionsWrapper = document.getElementById('permissions-wrapper');

            function toggleFields() {
                const isDataEntry = ['data_entry_tourism_establishments', 'data_entry_attractions_routes'].includes(accountType.value);
                governorateWrapper.style.display = (!isSuperAdmin.checked && isDataEntry) ? 'block' : 'none';
                permissionsWrapper.style.display = (!isSuperAdmin.checked && !isDataEntry) ? 'block' : 'none';
            }

            isSuperAdmin.addEventListener('change', toggleFields);
            accountType.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
@endpush
