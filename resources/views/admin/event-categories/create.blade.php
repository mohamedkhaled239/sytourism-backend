{{-- resources/views/admin/event-categories/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'إضافة تصنيف جديد')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.event-categories.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">الاسم (English)</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">الاسم (عربي)</label>
                    <input type="text" name="name_ar" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">اللون</label>
                    <input type="color" name="color" class="form-control" value="#667eea" required>
                </div>

                <button type="submit" class="btn btn-primary">حفظ التصنيف</button>
                <a href="{{ route('admin.event-categories.index') }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
@endsection
