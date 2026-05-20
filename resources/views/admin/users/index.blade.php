@extends('admin.layouts.app')

@section('title', 'المستخدمون')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">قائمة المستخدمين</h5>
                <a href="{{ route('admin.users.export') }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-2"></i> تصدير إكسل
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم الكامل</th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الهاتف</th>
                        <th>النوع</th>
                        <th>حالة المستثمر</th>
                        <th>التحقق</th>
                        <th>الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr class="{{ $user->user_type === 'investor' && !$user->is_approved ? 'table-warning' : '' }}">
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->full_name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                <span class="badge bg-{{ $user->user_type == 'investor' ? 'success' : 'info' }}">
                                    {{ $user->user_type == 'investor' ? 'مستثمر' : 'سائح' }}
                                </span>
                            </td>
                            <td>
                                @if($user->user_type === 'investor')
                                    <span class="badge bg-{{ $user->is_approved ? 'success' : 'warning text-dark' }}">
                                        {{ $user->is_approved ? 'تمت الموافقة' : 'بانتظار الموافقة' }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">محقق</span>
                                @else
                                    <span class="badge bg-secondary">غير محقق</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($user->user_type === 'investor' && !$user->is_approved)
                                    <form action="{{ route('admin.users.approve-investor', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('اعتماد هذا المستثمر الآن؟')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </div>
    </div>
@endsection
