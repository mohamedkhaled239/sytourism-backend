@extends('public.layouts.app')

@section('title', 'الاستثمارات')

@push('styles')
    <style>
        .page-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .investment-card {
            border: 1px solid #ececec;
            border-radius: 14px;
            overflow: hidden;
            height: 100%;
            background: white;
        }

        .investment-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .investment-card-body {
            padding: 18px;
        }

        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tag-item {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            background: #f1f5ed;
            color: #3E5828;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">الاستثمارات</h2>
                @if($selectedLocation)
                    <p class="text-muted mb-0">الاستثمارات المرتبطة بالموقع: {{ $selectedLocation->name_ar }}</p>
                @else
                    <p class="text-muted mb-0">الاستثمارات المضافة من لوحة تحكم الأدمن والمنشورة للمستثمرين.</p>
                @endif
            </div>

            <div class="d-flex gap-2 flex-wrap">
                @if($selectedLocation)
                    <a href="{{ route('investor.locations.show', $selectedLocation->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right ms-1"></i> العودة لتفاصيل الموقع
                    </a>
                @endif

                <a href="{{ route('investor.dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-map ms-1"></i> العودة للوحة المستثمر
                </a>
            </div>
        </div>

        <div class="page-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong>إجمالي الاستثمارات:</strong> {{ $investments->total() }}
                </div>

                @if($selectedLocation)
                    <a href="{{ route('investor.investments.index') }}" class="btn btn-sm btn-outline-secondary">إلغاء فلتر الموقع</a>
                @endif
            </div>
        </div>

        @if($investments->isEmpty())
            <div class="page-card">
                <div class="alert alert-light border mb-0">لا توجد استثمارات مطابقة حالياً.</div>
            </div>
        @else
            <div class="row">
                @foreach($investments as $investment)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="investment-card">
                            @if($investment->main_image_url)
                                <img src="{{ $investment->main_image_url }}" alt="{{ $investment->title_ar }}">
                            @endif

                            <div class="investment-card-body">
                                <h5 class="mb-3">{{ $investment->title_ar }}</h5>
                                <p class="text-muted">{{ \Illuminate\Support\Str::limit($investment->description_ar, 180) }}</p>

                                @if($investment->categories->isNotEmpty())
                                    <div class="mb-3">
                                        <div class="small fw-bold mb-2">التصنيفات</div>
                                        <div class="tag-list">
                                            @foreach($investment->categories as $category)
                                                <span class="tag-item">{{ $category->name_ar }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($investment->locations->isNotEmpty())
                                    <div>
                                        <div class="small fw-bold mb-2">المواقع المرتبطة</div>
                                        <div class="tag-list">
                                            @foreach($investment->locations->take(4) as $location)
                                                <a href="{{ route('investor.locations.show', $location->id) }}" class="tag-item text-decoration-none">
                                                    {{ $location->name_ar }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $investments->links() }}
            </div>
        @endif
    </div>
@endsection
