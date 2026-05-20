@extends('admin.layouts.app')

@section('title', 'الحسابات الإدارية')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">قائمة الحسابات الإدارية</h5>
                <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> إضافة حساب جديد
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>نوع الحساب</th>
                        <th>المحافظة</th>
                        <th>آخر تسجيل دخول</th>
                        <th>الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($admins as $admin)
                        <tr>
                            <td>{{ $admin->id }}</td>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>
                                <span class="badge bg-{{ $admin->isDataEntry() ? 'info' : ($admin->is_super_admin ? 'danger' : 'primary') }}">
                                    {{ $admin->accountTypeLabel() }}
                                </span>
                            </td>
                            <td>{{ $admin->governorate->name_ar ?? 'غير محدد' }}</td>
                            <td>{{ $admin->last_login_at ? $admin->last_login_at->format('Y-m-d H:i') : 'لم يسجل بعد' }}</td>
                            <td>
                                @if($admin->id !== auth()->guard('admin')->id())
                                    <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الحساب؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary">أنت</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{ $admins->links() }}
        </div>
    </div>
@endsection
