@extends('admin.layouts.app')

@section('title', 'خريطة المواقع')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous" />

    <!-- MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

    <style>
        #map {
            height: 600px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .map-controls {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .filter-group {
            margin-bottom: 15px;
        }

        .filter-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .filter-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .filter-btn {
            padding: 6px 12px;
            border: 1px solid #dee2e6;
            background: white;
            color: #495057;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
        }

        .filter-btn:hover {
            background: #f8f9fa;
            border-color: #3E5828;
        }

        .filter-btn.active {
            background: #3E5828;
            border-color: #3E5828;
            color: white;
        }

        .search-box {
            width: 100%;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-box:focus {
            outline: none;
            border-color: #3E5828;
            box-shadow: 0 0 0 2px rgba(62, 88, 40, 0.1);
        }

        .map-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 4px;
            margin-top: 15px;
            font-size: 14px;
            color: #6c757d;
        }

        .leaflet-popup-content {
            width: 250px !important;
        }

        .popup-content {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .popup-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #3E5828;
        }

        .popup-address {
            color: #6c757d;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .popup-badges {
            margin-bottom: 8px;
        }

        .popup-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            color: white;
            margin-right: 4px;
            margin-bottom: 4px;
        }

        .popup-actions {
            text-align: center;
            margin-top: 10px;
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .popup-btn {
            background: #3E5828;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            display: inline-block;
            flex: 1;
            text-align: center;
        }

        .popup-btn:hover {
            background: #8A6B4E;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
            transition: all 0.2s;
        }

        .custom-marker {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .reset-view-btn {
            position: absolute;
            top: 80px;
            right: 10px;
            z-index: 1000;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .reset-view-btn:hover {
            background: #f8f9fa;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- أدوات التحكم -->
        <div class="map-controls">
            <div class="row">
                <div class="col-md-4">
                    <div class="filter-group">
                        <div class="filter-label">البحث في المواقع</div>
                        <input type="text" class="search-box" id="location-search"
                               placeholder="ابحث عن موقع معين...">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="filter-group">
                        <div class="filter-label">فلترة حسب نوع السياحة</div>
                        <div class="filter-buttons" id="tourism-filters">
                            <button class="filter-btn active" data-filter="all">جميع الأنواع</button>
                            @if($locations->isNotEmpty())
                                @foreach($locations->pluck('tourismType')->filter()->unique('id') as $type)
                                    <button class="filter-btn" data-filter="tourism-{{ $type->id }}"
                                            style="border-color: {{ $type->color ?? '#3E5828' }}">
                                        {{ $type->name_ar ?? 'غير محدد' }}
                                    </button>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="filter-group">
                        <div class="filter-label">فلترة حسب المحافظة</div>
                        <div class="filter-buttons" id="governorate-filters">
                            <button class="filter-btn active" data-filter="all">جميع المحافظات</button>
                            @if($locations->isNotEmpty())
                                @foreach($locations->pluck('governorate')->filter()->unique('id') as $gov)
                                    <button class="filter-btn" data-filter="gov-{{ $gov->id }}">
                                        {{ $gov->name_ar ?? 'غير محدد' }}
                                    </button>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="map-stats">
                <span>إجمالي المواقع: <strong id="total-locations">{{ $locations->count() }}</strong></span>
                <span>المواقع المعروضة: <strong id="visible-locations">{{ $locations->count() }}</strong></span>
                <button class="btn btn-sm btn-outline-primary" id="reset-filters">إعادة تعيين الفلاتر</button>
            </div>
        </div>

        <!-- الخريطة -->
        <div class="card">
            <div class="card-body">
                <div style="position: relative;">
                    <div id="map"></div>
                    <button class="reset-view-btn" id="reset-view" title="العودة للعرض الافتراضي">
                        <i class="fas fa-home"></i> العرض الكامل
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- البيانات مخفية في الصفحة -->
    <script type="application/json" id="locations-data">
        {!! json_encode($locations->map(function($location) {
            return [
                'id' => $location->id,
                'name_ar' => $location->name_ar ?? '',
                'name' => $location->name ?? '',
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'address_ar' => $location->address_ar ?? '',
                'address' => $location->address ?? '',
                'description_ar' => $location->description_ar ?? '',
                'phone' => $location->phone ?? '',
                'website' => $location->website ?? '',
                'rating' => $location->rating ?? 0,
                'tourism_type' => $location->tourismType ? [
                    'id' => $location->tourismType->id,
                    'name_ar' => $location->tourismType->name_ar,
                    'color' => $location->tourismType->color ?? '#3E5828'
                ] : null,
                'governorate' => $location->governorate ? [
                    'id' => $location->governorate->id,
                    'name_ar' => $location->governorate->name_ar
                ] : null,
                'location_types' => $location->locationTypes->map(function($lt) {
                    return [
                        'id'            => $lt->id,
                        'name_ar'       => $lt->name_ar,
                        'color'         => $lt->color ?? '#3E5828',
                        'pin_image_url' => $lt->pin_image_url,
                    ];
                })->toArray(),
                'categories' => $location->categories->map(function($cat) {
                    return [
                        'id' => $cat->id,
                        'name_ar' => $cat->name_ar,
                        'color' => $cat->color ?? '#6c757d'
                    ];
                })->toArray()
            ];
        })->toArray(), JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>

    <!-- MarkerCluster JS -->
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <script>
        // قراءة بيانات المواقع
        const locations = JSON.parse(document.getElementById('locations-data').textContent);

        let map, markersGroup, allMarkers = [];
        let originalBounds;

        // تهيئة الخريطة
        function initMap() {
            // إنشاء الخريطة مع التركيز على سوريا
            map = L.map('map').setView([34.802075, 38.996815], 7);

            // إضافة طبقة الخريطة
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18
            }).addTo(map);

            // إنشاء مجموعة العلامات مع التجميع
            markersGroup = L.markerClusterGroup({
                chunkedLoading: true,
                maxClusterRadius: 50
            });

            // إضافة المواقع إلى الخريطة
            addLocationsToMap();

            // حفظ الحدود الأصلية
            if (allMarkers.length > 0) {
                originalBounds = markersGroup.getBounds();
            }

            map.addLayer(markersGroup);

            // تحديث أحجام العلامات عند تغيير التقريب (الزوم)
            map.on('zoomend', updateMarkerSizes);
            updateMarkerSizes();
        }

        // دالة لتعديل حجم العلامات ديناميكياً حسب مستوى التقريب (الزوم)
        function updateMarkerSizes() {
            const zoom = map.getZoom();
            
            // تحديد حجم الدبوس بناءً على مستوى الزوم
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

            allMarkers.forEach(function(item) {
                const marker = item.marker;
                const location = item.data;
                const pinType = location.location_types && location.location_types.find(lt => lt.pin_image_url);

                if (pinType && pinType.pin_image_url) {
                    const newIcon = L.icon({
                        iconUrl: pinType.pin_image_url,
                        iconSize: [size, size],
                        iconAnchor: [size / 2, size],
                        popupAnchor: [0, -size]
                    });
                    marker.setIcon(newIcon);
                }
            });
        }

        // إضافة المواقع إلى الخريطة
        function addLocationsToMap() {
            locations.forEach(function(location) {
                if (location.latitude && location.longitude) {
                    const marker = createMarker(location);
                    if (marker) {
                        allMarkers.push({
                            marker: marker,
                            data: location
                        });
                        markersGroup.addLayer(marker);
                    }
                }
            });
        }

        // إنشاء علامة للموقع
        function createMarker(location) {
            try {
                let markerIcon;

                // 1. محاولة استخدام صورة دبوس نوع الموقع (أول نوع يحمل صورة دبوس)
                const pinType = location.location_types && location.location_types.find(lt => lt.pin_image_url);

                if (pinType && pinType.pin_image_url) {
                    markerIcon = L.icon({
                        iconUrl: pinType.pin_image_url,
                        iconSize: [56, 56],
                        iconAnchor: [28, 56],
                        popupAnchor: [0, -56]
                    });
                } else {
                    // 2. Fallback: دائرة ملوّنة حسب نوع السياحة
                    const color = location.tourism_type ? location.tourism_type.color : '#6c757d';
                    markerIcon = L.divIcon({
                        className: 'custom-div-icon',
                        html: '<div class="custom-marker" style="background-color: ' + color + '"></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    });
                }

                const marker = L.marker([location.latitude, location.longitude], {
                    icon: markerIcon
                });

                // إنشاء محتوى النافذة المنبثقة
                const popupContent = createPopupContent(location);
                marker.bindPopup(popupContent, {
                    maxWidth: 300,
                    className: 'custom-popup'
                });

                // إضافة بيانات الموقع للعلامة للفلترة
                marker.locationData = location;

                return marker;
            } catch (error) {
                console.error('خطأ في إنشاء العلامة للموقع:', location.name_ar, error);
                return null;
            }
        }

        // إنشاء محتوى النافذة المنبثقة
        function createPopupContent(location) {
            let content = '<div class="popup-content">';

            // العنوان
            content += '<div class="popup-title">' + location.name_ar + '</div>';

            // العنوان
            if (location.address_ar) {
                content += '<div class="popup-address"><i class="fas fa-map-marker-alt"></i> ' + location.address_ar + '</div>';
            }

            // المحافظة ونوع السياحة
            content += '<div class="popup-badges">';
            if (location.governorate) {
                content += '<span class="popup-badge" style="background-color: #6c757d">' + location.governorate.name_ar + '</span>';
            }
            if (location.tourism_type) {
                content += '<span class="popup-badge" style="background-color: ' + location.tourism_type.color + '">' + location.tourism_type.name_ar + '</span>';
            }
            content += '</div>';

            // التصنيفات
            if (location.categories && location.categories.length > 0) {
                content += '<div class="popup-badges">';
                location.categories.forEach(function(category) {
                    content += '<span class="popup-badge" style="background-color: ' + category.color + '">' + category.name_ar + '</span>';
                });
                content += '</div>';
            }

            // التقييم
            if (location.rating) {
                content += '<div style="margin-bottom: 8px;">';
                content += '<i class="fas fa-star" style="color: #ffc107;"></i> ';
                content += '<span>' + location.rating + '/5</span>';
                content += '</div>';
            }

            // معلومات الاتصال
            if (location.phone) {
                content += '<div style="margin-bottom: 4px; font-size: 12px;">';
                content += '<i class="fas fa-phone"></i> ' + location.phone;
                content += '</div>';
            }

            if (location.website) {
                content += '<div style="margin-bottom: 8px; font-size: 12px;">';
                content += '<i class="fas fa-globe"></i> <a href="' + location.website + '" target="_blank">الموقع الإلكتروني</a>';
                content += '</div>';
            }

            // أزرار الإجراءات
            content += '<div class="popup-actions">';
            content += '<a href="/admin/map/' + location.id + '" class="popup-btn" style="background: #17a2b8; margin-left: 5px;">';
            content += '<i class="fas fa-eye"></i> عرض';
            content += '</a>';
            content += '<a href="/admin/locations/' + location.id + '/edit" class="popup-btn">';
            content += '<i class="fas fa-edit"></i> تعديل';
            content += '</a>';
            content += '</div>';

            content += '</div>';

            return content;
        }

        // فلترة المواقع
        function filterLocations() {
            const searchTerm = document.getElementById('location-search').value.toLowerCase();
            const activeTourismFilter = document.querySelector('#tourism-filters .filter-btn.active')?.dataset.filter;
            const activeGovernorateFilter = document.querySelector('#governorate-filters .filter-btn.active')?.dataset.filter;

            let visibleCount = 0;

            // مسح جميع العلامات
            markersGroup.clearLayers();

            // إضافة العلامات المفلترة
            allMarkers.forEach(function(item) {
                const location = item.data;
                let shouldShow = true;

                // فلترة البحث النصي
                if (searchTerm && !location.name_ar.toLowerCase().includes(searchTerm)
                    && !(location.address_ar && location.address_ar.toLowerCase().includes(searchTerm))) {
                    shouldShow = false;
                }

                // فلترة نوع السياحة
                if (activeTourismFilter && activeTourismFilter !== 'all') {
                    const tourismId = activeTourismFilter.replace('tourism-', '');
                    if (!location.tourism_type || location.tourism_type.id != tourismId) {
                        shouldShow = false;
                    }
                }

                // فلترة المحافظة
                if (activeGovernorateFilter && activeGovernorateFilter !== 'all') {
                    const govId = activeGovernorateFilter.replace('gov-', '');
                    if (!location.governorate || location.governorate.id != govId) {
                        shouldShow = false;
                    }
                }

                if (shouldShow) {
                    markersGroup.addLayer(item.marker);
                    visibleCount++;
                }
            });

            // تحديث العداد
            document.getElementById('visible-locations').textContent = visibleCount;

            // ضبط عرض الخريطة إذا كان هناك نتائج
            if (visibleCount > 0) {
                setTimeout(() => {
                    if (markersGroup.getBounds().isValid()) {
                        map.fitBounds(markersGroup.getBounds(), { padding: [20, 20] });
                    }
                }, 100);
            }
        }

        // أحداث الفلترة
        document.addEventListener('DOMContentLoaded', function() {
            initMap();

            // البحث النصي
            document.getElementById('location-search').addEventListener('input', filterLocations);

            // فلاتر نوع السياحة
            document.querySelectorAll('#tourism-filters .filter-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('#tourism-filters .filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterLocations();
                });
            });

            // فلاتر المحافظة
            document.querySelectorAll('#governorate-filters .filter-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('#governorate-filters .filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterLocations();
                });
            });

            // إعادة تعيين الفلاتر
            document.getElementById('reset-filters').addEventListener('click', function() {
                document.getElementById('location-search').value = '';
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.filter-btn[data-filter="all"]').forEach(btn => btn.classList.add('active'));
                filterLocations();
            });

            // العودة للعرض الكامل
            document.getElementById('reset-view').addEventListener('click', function() {
                if (originalBounds && originalBounds.isValid()) {
                    map.fitBounds(originalBounds, { padding: [20, 20] });
                } else {
                    map.setView([34.802075, 38.996815], 7);
                }
            });
        });
    </script>
@endpush
