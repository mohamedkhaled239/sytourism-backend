@extends('admin.layouts.app')

@section('title', 'تفاصيل المستخدم')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">تفاصيل المستخدم: {{ $user->full_name }}</h5>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">العودة</a>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><th width="40%">الاسم الكامل:</th><td>{{ $user->full_name }}</td></tr>
                                <tr><th>اسم المستخدم:</th><td>{{ $user->username }}</td></tr>
                                <tr><th>البريد الإلكتروني:</th><td>{{ $user->email }}</td></tr>
                                <tr><th>رقم الهاتف:</th><td>{{ $user->phone ?? 'غير محدد' }}</td></tr>
                                <tr><th>الدولة:</th><td>{{ $user->country ?? 'غير محدد' }}</td></tr>
                                <tr>
                                    <th>نوع المستخدم:</th>
                                    <td>
                                        <span class="badge bg-{{ $user->user_type == 'investor' ? 'success' : 'info' }}">
                                            {{ $user->user_type == 'investor' ? 'مستثمر' : 'سائح' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">حالة التحقق:</th>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">محقق</span>
                                            <small class="text-muted d-block">{{ $user->email_verified_at->format('Y-m-d H:i') }}</small>
                                        @else
                                            <span class="badge bg-secondary">غير محقق</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>اعتماد المستثمر:</th>
                                    <td>
                                        @if($user->user_type === 'investor')
                                            <span class="badge bg-{{ $user->is_approved ? 'success' : 'warning text-dark' }}">
                                                {{ $user->is_approved ? 'تمت الموافقة' : 'بانتظار الموافقة' }}
                                            </span>
                                            @if($user->approved_at)
                                                <small class="text-muted d-block">{{ $user->approved_at->format('Y-m-d H:i') }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">غير مطلوب</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr><th>الإشعارات:</th><td>{{ $user->notifications_enabled ? 'مفعلة' : 'معطلة' }}</td></tr>
                                <tr><th>تاريخ التسجيل:</th><td>{{ $user->created_at->format('Y-m-d H:i') }}</td></tr>
                                <tr><th>آخر تحديث:</th><td>{{ $user->updated_at->format('Y-m-d H:i') }}</td></tr>
                                <tr><th>آخر تسجيل دخول:</th><td>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'لم يسجل بعد' }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">إجراءات</h6>

                    @if($user->user_type === 'investor' && !$user->is_approved)
                        <form action="{{ route('admin.users.approve-investor', $user->id) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('اعتماد هذا المستثمر الآن؟')">
                                <i class="fas fa-check me-2"></i> الموافقة على المستثمر
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i> حذف المستخدم
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
