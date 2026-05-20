@extends('public.layouts.app')

@section('title', 'تفاصيل الموقع: ' . $location->name_ar)

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous" />

    <style>
        .location-map {
            height: 400px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .info-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-icon {
            width: 36px;
            text-align: center;
            color: #3E5828;
            font-size: 16px;
            padding-top: 3px;
        }

        .badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .custom-badge {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            color: white;
            font-weight: 600;
        }

        .related-items {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }

        .related-item {
            background: white;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            border-right: 4px solid #3E5828;
        }

        .related-item:last-child {
            margin-bottom: 0;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .coordinates {
            font-family: monospace;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 6px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">{{ $location->name_ar }}</h2>
                @if($location->name && $location->name !== $location->name_ar)
                    <p class="text-muted mb-0">{{ $location->name }}</p>
                @endif
            </div>

            <div class="action-buttons">
                <a href="{{ route('investor.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right ms-1"></i> العودة لكل المواقع
                </a>
                <a href="{{ route('investor.investments.index', ['location' => $location->id]) }}" class="btn btn-primary">
                    <i class="fas fa-chart-line ms-1"></i> استثمارات الموقع
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="mb-3">المعلومات الأساسية</h5>

                    @if($location->address_ar)
                        <div class="info-row">
                            <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <div class="fw-bold">العنوان</div>
                                <div>{{ $location->address_ar }}</div>
                                @if($location->address && $location->address !== $location->address_ar)
                                    <div class="text-muted">{{ $location->address }}</div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($location->governorate)
                        <div class="info-row">
                            <div class="info-icon"><i class="fas fa-map"></i></div>
                            <div>
                                <div class="fw-bold">المحافظة</div>
                                <div>{{ $location->governorate->name_ar }}</div>
                            </div>
                        </div>
                    @endif

                    @if($location->city)
                        <div class="info-row">
                            <div class="info-icon"><i class="fas fa-city"></i></div>
                            <div>
                                <div class="fw-bold">المدينة</div>
                                <div>{{ $location->city->name_ar }}</div>
                            </div>
                        </div>
                    @endif

                    @if($location->tourismType)
                        <div class="info-row">
                            <div class="info-icon"><i class="fas fa-route"></i></div>
                            <div>
                                <div class="fw-bold">نوع السياحة</div>
                                <div class="badge-container">
                                    <span class="custom-badge" style="background-color: {{ $location->tourismType->color ?? '#3E5828' }}">
                                        {{ $location->tourismType->name_ar }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($location->phone)
                        <div class="info-row">
                            <div class="info-icon"><i class="fas fa-phone"></i></div>
                            <div>
                                <div class="fw-bold">الهاتف</div>
                                <div><a href="tel:{{ $location->phone }}">{{ $location->phone }}</a></div>
                            </div>
                        </div>
                    @endif

                    @if($location->website)
                        <div class="info-row">
                            <div class="info-icon"><i class="fas fa-globe"></i></div>
                            <div>
                                <div class="fw-bold">الموقع الإلكتروني</div>
                                <div><a href="{{ $location->website }}" target="_blank">{{ $location->website }}</a></div>
                            </div>
                        </div>
                    @endif

                    @if($location->email)
                        <div class="info-row">
                            <div class="info-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <div class="fw-bold">البريد الإلكتروني</div>
                                <div><a href="mailto:{{ $location->email }}">{{ $location->email }}</a></div>
                            </div>
                        </div>
                    @endif

                    <div class="info-row">
                        <div class="info-icon"><i class="fas fa-crosshairs"></i></div>
                        <div>
                            <div class="fw-bold">الإحداثيات</div>
                            <span class="coordinates">{{ $location->latitude }}, {{ $location->longitude }}</span>
                        </div>
                    </div>
                </div>

                @if($location->categories->isNotEmpty() || $location->locationTypes->isNotEmpty())
                    <div class="info-card">
                        <h5 class="mb-3">التصنيفات</h5>

                        @if($location->categories->isNotEmpty())
                            <div class="mb-3">
                                <strong>فئات الموقع</strong>
                                <div class="badge-container">
                                    @foreach($location->categories as $category)
                                        <span class="custom-badge" style="background-color: {{ $category->color ?? '#6c757d' }}">
                                            {{ $category->name_ar }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($location->locationTypes->isNotEmpty())
                            <div>
                                <strong>أنواع الموقع</strong>
                                <div class="badge-container">
                                    @foreach($location->locationTypes as $type)
                                        <span class="custom-badge" style="background-color: {{ $type->color ?? '#3E5828' }}">
                                            {{ $type->name_ar }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="mb-3">موقعه على الخريطة</h5>
                    <div id="location-map" class="location-map"></div>
                </div>
            </div>
        </div>

        @if($location->description_ar || $location->features_ar)
            <div class="info-card">
                <h5 class="mb-3">تفاصيل إضافية</h5>

                @if($location->description_ar)
                    <div class="mb-3">
                        <h6>الوصف</h6>
                        <p class="mb-1">{{ $location->description_ar }}</p>
                        @if($location->description && $location->description !== $location->description_ar)
                            <p class="text-muted mb-0">{{ $location->description }}</p>
                        @endif
                    </div>
                @endif

                @if($location->features_ar)
                    <div>
                        <h6>المميزات</h6>
                        <p class="mb-1">{{ $location->features_ar }}</p>
                        @if($location->features && $location->features !== $location->features_ar)
                            <p class="text-muted mb-0">{{ $location->features }}</p>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        @if($location->events->isNotEmpty() || $location->investments->isNotEmpty())
            <div class="info-card">
                <h5 class="mb-3">المحتوى المرتبط بالموقع</h5>

                @if($location->events->isNotEmpty())
                    <div class="related-items">
                        <h6><i class="fas fa-calendar-alt ms-1"></i> الأحداث المرتبطة ({{ $location->events->count() }})</h6>
                        @foreach($location->events->take(5) as $event)
                            <div class="related-item">
                                <strong>{{ $event->title_ar }}</strong>
                                @if($event->start_date && $event->end_date)
                                    <div class="small text-muted mt-1">
                                        {{ $event->start_date->format('Y-m-d') }} - {{ $event->end_date->format('Y-m-d') }}
                                    </div>
                                @endif
                                @if($event->description_ar)
                                    <div class="small text-muted mt-2">{{ \Illuminate\Support\Str::limit($event->description_ar, 120) }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($location->investments->isNotEmpty())
                    <div class="related-items">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <h6 class="mb-0"><i class="fas fa-chart-line ms-1"></i> الاستثمارات المرتبطة ({{ $location->investments->count() }})</h6>
                            <a href="{{ route('investor.investments.index', ['location' => $location->id]) }}" class="btn btn-sm btn-outline-primary">
                                عرض كل الاستثمارات
                            </a>
                        </div>

                        @foreach($location->investments->take(5) as $investment)
                            <div class="related-item">
                                <strong>{{ $investment->title_ar }}</strong>
                                <div class="small text-muted mt-2">{{ \Illuminate\Support\Str::limit($investment->description_ar, 140) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('location-map').setView([{{ $location->latitude }}, {{ $location->longitude }}], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18
            }).addTo(map);

            const markerColor = '{{ $location->tourismType ? $location->tourismType->color : "#3E5828" }}';

            const customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="width:30px;height:30px;border-radius:50%;background-color:${markerColor};border:3px solid white;box-shadow:0 2px 4px rgba(0,0,0,0.3);"></div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });

            const marker = L.marker([{{ $location->latitude }}, {{ $location->longitude }}], { icon: customIcon }).addTo(map);
            marker.bindPopup(`<div style="text-align:center;"><strong>{{ $location->name_ar }}</strong><br>{{ $location->address_ar ?: 'موقع على الخريطة' }}</div>`).openPopup();

            L.circle([{{ $location->latitude }}, {{ $location->longitude }}], {
                color: markerColor,
                fillColor: markerColor,
                fillOpacity: 0.1,
                radius: 200
            }).addTo(map);
        });
    </script>
@endpush
