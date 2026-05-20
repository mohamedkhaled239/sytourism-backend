@extends('admin.layouts.app')

@section('title', 'تفاصيل الموقع: ' . $location->name_ar)

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous" />

    <style>
        .location-map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .info-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .info-icon {
            width: 40px;
            text-align: center;
            color: #3E5828;
            font-size: 16px;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .info-value {
            color: #666;
        }

        .badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .custom-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            color: white;
            font-weight: 500;
        }

        .rating-stars {
            color: #ffc107;
            margin-left: 8px;
        }

        .coordinates {
            font-family: monospace;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
        }

        .related-items {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .related-items h6 {
            margin-bottom: 10px;
            color: #3E5828;
        }

        .related-item {
            background: white;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 8px;
            border-left: 3px solid #3E5828;
        }

        .related-item:last-child {
            margin-bottom: 0;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .action-buttons .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>{{ $location->name_ar }}</h2>
                @if($location->name && $location->name !== $location->name_ar)
                    <p class="text-muted mb-0">{{ $location->name }}</p>
                @endif
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.map.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i> العودة للخريطة
                </a>
                <a href="{{ route('admin.locations.edit', $location->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i> تعديل الموقع
                </a>
            </div>
        </div>

        <div class="row">
            <!-- معلومات الموقع -->
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="mb-3">معلومات أساسية</h5>

                    @if($location->address_ar)
                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">العنوان</div>
                                <div class="info-value">{{ $location->address_ar }}</div>
                                @if($location->address && $location->address !== $location->address_ar)
                                    <div class="info-value text-muted">{{ $location->address }}</div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($location->governorate)
                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-map"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">المحافظة</div>
                                <div class="info-value">{{ $location->governorate->name_ar }}</div>
                            </div>
                        </div>
                    @endif

                    @if($location->tourismType)
                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-route"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">نوع السياحة</div>
                                <div class="info-value">
                                    <span class="custom-badge" style="background-color: {{ $location->tourismType->color }}">
                                        {{ $location->tourismType->name_ar }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($location->phone)
                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">رقم الهاتف</div>
                                <div class="info-value">
                                    <a href="tel:{{ $location->phone }}">{{ $location->phone }}</a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($location->website)
                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">الموقع الإلكتروني</div>
                                <div class="info-value">
                                    <a href="{{ $location->website }}" target="_blank">{{ $location->website }}</a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($location->email)
                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">البريد الإلكتروني</div>
                                <div class="info-value">
                                    <a href="mailto:{{ $location->email }}">{{ $location->email }}</a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($location->rating)
                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">التقييم</div>
                                <div class="info-value">
                                    {{ $location->rating }}/5
                                    <span class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $location->rating ? '' : '-o' }}"></i>
                                        @endfor
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="info-row">
                        <div class="info-icon">
                            <i class="fas fa-crosshairs"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">الإحداثيات</div>
                            <div class="info-value">
                                <span class="coordinates">
                                    {{ $location->latitude }}, {{ $location->longitude }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- التصنيفات وأنواع المواقع -->
                @if($location->categories->count() > 0 || $location->locationTypes->count() > 0)
                    <div class="info-card">
                        <h5 class="mb-3">التصنيفات</h5>

                        @if($location->categories->count() > 0)
                            <div class="mb-3">
                                <strong>فئات الموقع:</strong>
                                <div class="badge-container">
                                    @foreach($location->categories as $category)
                                        <span class="custom-badge" style="background-color: {{ $category->color }}">
                                            {{ $category->name_ar }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($location->locationTypes->count() > 0)
                            <div>
                                <strong>أنواع الموقع:</strong>
                                <div class="badge-container">
                                    @foreach($location->locationTypes as $type)
                                        <span class="custom-badge" style="background-color: {{ $type->color }}">
                                            <i class="{{ $type->icon }} me-1"></i>
                                            {{ $type->name_ar }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- الخريطة -->
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="mb-3">موقع على الخريطة</h5>
                    <div id="location-map" class="location-map"></div>
                </div>
            </div>
        </div>

        <!-- الوصف والميزات -->
        @if($location->description_ar || $location->features_ar)
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h5 class="mb-3">تفاصيل إضافية</h5>

                        @if($location->description_ar)
                            <div class="mb-3">
                                <h6>الوصف:</h6>
                                <p>{{ $location->description_ar }}</p>
                                @if($location->description && $location->description !== $location->description_ar)
                                    <p class="text-muted">{{ $location->description }}</p>
                                @endif
                            </div>
                        @endif

                        @if($location->features_ar)
                            <div>
                                <h6>الميزات:</h6>
                                <p>{{ $location->features_ar }}</p>
                                @if($location->features && $location->features !== $location->features_ar)
                                    <p class="text-muted">{{ $location->features }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- الأحداث والاستثمارات المرتبطة -->
        @if($location->events->count() > 0 || $location->investments->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="info-card">
                        <h5 class="mb-3">المحتوى المرتبط</h5>

                        @if($location->events->count() > 0)
                            <div class="related-items">
                                <h6><i class="fas fa-calendar-alt me-2"></i>الأحداث ({{ $location->events->count() }})</h6>
                                @foreach($location->events->take(5) as $event)
                                    <div class="related-item">
                                        <strong>{{ $event->title_ar }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $event->start_date->format('Y-m-d') }} - {{ $event->end_date->format('Y-m-d') }}
                                        </small>
                                    </div>
                                @endforeach
                                @if($location->events->count() > 5)
                                    <div class="text-center mt-2">
                                        <a href="{{ route('admin.events.index') }}?location={{ $location->id }}" class="btn btn-sm btn-outline-primary">
                                            عرض جميع الأحداث
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($location->investments->count() > 0)
                            <div class="related-items">
                                <h6><i class="fas fa-chart-line me-2"></i>الاستثمارات ({{ $location->investments->count() }})</h6>
                                @foreach($location->investments->take(5) as $investment)
                                    <div class="related-item">
                                        <strong>{{ $investment->title_ar }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($investment->description_ar, 100) }}</small>
                                    </div>
                                @endforeach
                                @if($location->investments->count() > 5)
                                    <div class="text-center mt-2">
                                        <a href="{{ route('admin.investments.index') }}?location={{ $location->id }}" class="btn btn-sm btn-outline-primary">
                                            عرض جميع الاستثمارات
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // إنشاء الخريطة
            const map = L.map('location-map').setView([{{ $location->latitude }}, {{ $location->longitude }}], 15);

            // إضافة طبقة الخريطة
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18
            }).addTo(map);

            // تحديد لون العلامة
            const markerColor = '{{ $location->tourismType ? $location->tourismType->color : "#3E5828" }}';

            @php
                $pinType = $location->locationTypes->first(function($lt) {
                    return !empty($lt->pin_image_url);
                });
            @endphp

            let customIcon;
            @if($pinType)
                customIcon = L.icon({
                    iconUrl: '{{ $pinType->pin_image_url }}',
                    iconSize: [56, 56],
                    iconAnchor: [28, 56],
                    popupAnchor: [0, -56]
                });
            @else
                // إنشاء أيقونة مخصصة
                customIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="width: 30px; height: 30px; border-radius: 50%; background-color: ${markerColor}; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });
            @endif

            // إضافة العلامة
            const marker = L.marker([{{ $location->latitude }}, {{ $location->longitude }}], {
                icon: customIcon
            }).addTo(map);

            // تحديث حجم الدبوس ديناميكياً مع الزوم
            @if($pinType)
                function updateShowMarkerSize() {
                    const zoom = map.getZoom();
                    let size = 48;
                    if (zoom >= 8 && zoom <= 9) {
                        size = 56;
                    } else if (zoom >= 10 && zoom <= 11) {
                        size = 64;
                    } else if (zoom >= 12 && zoom <= 13) {
                        size = 76;
                    } else if (zoom >= 14) {
                        size = 88;
                    }
                    
                    const newIcon = L.icon({
                        iconUrl: '{{ $pinType->pin_image_url }}',
                        iconSize: [size, size],
                        iconAnchor: [size / 2, size],
                        popupAnchor: [0, -size]
                    });
                    marker.setIcon(newIcon);
                }
                
                map.on('zoomend', updateShowMarkerSize);
                updateShowMarkerSize();
            @endif

            // إضافة نافذة منبثقة
            const locationName = {!! json_encode($location->name_ar) !!};
            const locationAddress = {!! json_encode($location->address_ar ?: 'موقع على الخريطة') !!};

            const popupContent = `
                <div style="text-align: center;">
                    <strong>${locationName}</strong><br>
                    ${locationAddress}
                </div>
            `;
            marker.bindPopup(popupContent).openPopup();

            // إضافة دائرة حول الموقع
            L.circle([{{ $location->latitude }}, {{ $location->longitude }}], {
                color: markerColor,
                fillColor: markerColor,
                fillOpacity: 0.1,
                radius: 200
            }).addTo(map);
        });
    </script>
@endpush
