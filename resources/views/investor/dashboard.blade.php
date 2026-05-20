@extends('public.layouts.app')

@section('title', 'بوابة المستثمرين - لوحة التحكم')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

    <style>
        #map {
            height: 500px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .map-controls,
        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .filter-group {
            margin-bottom: 15px;
        }

        .filter-label {
            font-weight: 700;
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
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: #3E5828;
            border-color: #3E5828;
            color: white;
        }

        .search-box {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            font-size: 14px;
        }

        .search-box:focus {
            outline: none;
            border-color: #3E5828;
            box-shadow: 0 0 0 3px rgba(62, 88, 40, 0.12);
        }

        .map-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 10px;
            margin-top: 15px;
            font-size: 14px;
            color: #6c757d;
        }

        .leaflet-popup-content {
            width: 250px !important;
        }

        .popup-title {
            font-size: 16px;
            font-weight: 700;
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
            border-radius: 4px;
            font-size: 11px;
            color: white;
            margin-left: 4px;
            margin-bottom: 4px;
        }

        .popup-actions {
            margin-top: 10px;
            display: flex;
            justify-content: center;
        }

        .popup-btn {
            background: #17a2b8;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
        }

        .popup-btn:hover {
            color: white;
            text-decoration: none;
        }

        .custom-marker {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .reset-view-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 1000;
            background: white;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
        }

        .investment-card {
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
            background: white;
        }

        .investment-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .investment-card-body {
            padding: 16px;
        }

        @media (max-width: 768px) {
            .header-actions {
                width: 100%;
                justify-content: stretch;
            }

            .header-actions > * {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-1">مرحباً بك، {{ auth('web')->user()->name ?? auth('web')->user()->full_name ?? 'مستثمر' }}</h2>
            <p class="text-muted mb-0">يمكنك استعراض كل المواقع والاستثمارات من داخل بوابة المستثمرين.</p>
        </div>

        <div class="d-flex gap-2 flex-wrap header-actions">
            <a href="{{ route('investor.investments.index') }}" class="btn btn-primary">
                <i class="fas fa-chart-line ms-1"></i> الاستثمارات
            </a>

            <form action="{{ route('investor.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger">تسجيل الخروج</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="map-controls">
                <div class="row">
                    <div class="col-md-4">
                        <div class="filter-group">
                            <div class="filter-label">البحث في المواقع</div>
                            <input type="text" class="search-box" id="location-search" placeholder="ابحث عن موقع أو عنوان...">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="filter-group">
                            <div class="filter-label">فلترة حسب نوع السياحة</div>
                            <div class="filter-buttons" id="tourism-filters">
                                <button class="filter-btn active" data-filter="all">جميع الأنواع</button>
                                @foreach($allLocations->pluck('tourismType')->filter()->unique('id') as $type)
                                    <button class="filter-btn" data-filter="tourism-{{ $type->id }}">
                                        {{ $type->name_ar ?? 'غير محدد' }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="filter-group">
                            <div class="filter-label">فلترة حسب المحافظة</div>
                            <div class="filter-buttons" id="governorate-filters">
                                <button class="filter-btn active" data-filter="all">جميع المحافظات</button>
                                @foreach($allLocations->pluck('governorate')->filter()->unique('id') as $gov)
                                    <button class="filter-btn" data-filter="gov-{{ $gov->id }}">
                                        {{ $gov->name_ar ?? 'غير محدد' }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="map-stats">
                    <span>إجمالي المواقع: <strong id="total-locations">{{ $allLocations->count() }}</strong></span>
                    <span>المواقع المعروضة: <strong id="visible-locations">{{ $allLocations->count() }}</strong></span>
                    <button class="btn btn-sm btn-outline-primary" id="reset-filters">إعادة تعيين الفلاتر</button>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="summary-card">
                <h5 class="mb-3"><i class="fas fa-map text-success"></i> خريطة المواقع والمنشآت</h5>
                <div style="position: relative;">
                    <div id="map"></div>
                    <button class="reset-view-btn" id="reset-view" title="العودة للعرض الافتراضي">
                        <i class="fas fa-home"></i> العرض الكامل
                    </button>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="summary-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="mb-0"><i class="fas fa-briefcase text-warning"></i> أحدث الاستثمارات</h5>
                    <a href="{{ route('investor.investments.index') }}" class="btn btn-sm btn-outline-primary">عرض كل الاستثمارات</a>
                </div>

                @if($recentInvestments->isEmpty())
                    <div class="alert alert-light border mb-0">لا توجد استثمارات منشورة حالياً.</div>
                @else
                    <div class="row">
                        @foreach($recentInvestments as $investment)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="investment-card">
                                    @if($investment->main_image_url)
                                        <img src="{{ $investment->main_image_url }}" alt="{{ $investment->title_ar }}">
                                    @endif

                                    <div class="investment-card-body">
                                        <h6>{{ $investment->title_ar }}</h6>
                                        <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($investment->description_ar, 120) }}</p>

                                        @if($investment->locations->isNotEmpty())
                                            <div class="small text-muted">
                                                <i class="fas fa-map-marker-alt ms-1"></i>
                                                {{ $investment->locations->pluck('name_ar')->take(2)->implode(' - ') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-12">
            <div class="summary-card">
                <h5 class="mb-3"><i class="fas fa-map-marker-alt text-primary"></i> المواقع ({{ $locations->total() }})</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>اسم الموقع</th>
                                <th>النوع</th>
                                <th>العنوان</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($locations as $loc)
                                <tr>
                                    <td><strong>{{ $loc->name_ar }}</strong></td>
                                    <td>
                                        @if($loc->tourismType)
                                            <span class="badge" style="background-color: {{ $loc->tourismType->color ?? '#6c757d' }};">
                                                {{ $loc->tourismType->name_ar }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $loc->address_ar }}</td>
                                    <td>
                                        <a href="{{ route('investor.locations.show', $loc->id) }}" class="btn btn-sm btn-info text-white">
                                            <i class="fas fa-eye"></i> عرض التفاصيل
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $locations->links() }}
                </div>
            </div>
        </div>
    </div>

    <script type="application/json" id="locations-data">
        {!! json_encode($allLocations->map(function ($location) {
            return [
                'id' => $location->id,
                'name_ar' => $location->name_ar ?? '',
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'address_ar' => $location->address_ar ?? '',
                'phone' => $location->phone ?? '',
                'website' => $location->website ?? '',
                'rating' => $location->rating ?? 0,
                'tourism_type' => $location->tourismType ? [
                    'id' => $location->tourismType->id,
                    'name_ar' => $location->tourismType->name_ar,
                    'color' => $location->tourismType->color ?? '#3E5828',
                ] : null,
                'governorate' => $location->governorate ? [
                    'id' => $location->governorate->id,
                    'name_ar' => $location->governorate->name_ar,
                ] : null,
                'categories' => $location->categories->map(function ($cat) {
                    return [
                        'id' => $cat->id,
                        'name_ar' => $cat->name_ar,
                        'color' => $cat->color ?? '#6c757d',
                    ];
                })->toArray(),
            ];
        })->toArray(), JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <script>
        const locations = JSON.parse(document.getElementById('locations-data').textContent);
        const investorLocationBaseUrl = @json(url('/investor/locations'));

        let map;
        let markersGroup;
        let allMarkers = [];
        let originalBounds;

        function initMap() {
            map = L.map('map').setView([34.802075, 38.996815], 7);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18
            }).addTo(map);

            markersGroup = L.markerClusterGroup({
                chunkedLoading: true,
                maxClusterRadius: 50
            });

            addLocationsToMap();

            if (allMarkers.length > 0) {
                originalBounds = markersGroup.getBounds();
            }

            map.addLayer(markersGroup);
        }

        function addLocationsToMap() {
            locations.forEach(function (location) {
                if (!location.latitude || !location.longitude) {
                    return;
                }

                const marker = createMarker(location);
                if (!marker) {
                    return;
                }

                allMarkers.push({
                    marker: marker,
                    data: location
                });

                markersGroup.addLayer(marker);
            });
        }

        function createMarker(location) {
            try {
                const color = location.tourism_type ? location.tourism_type.color : '#6c757d';
                const customIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: '<div class="custom-marker" style="background-color: ' + color + '"></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });

                const marker = L.marker([location.latitude, location.longitude], { icon: customIcon });
                marker.bindPopup(createPopupContent(location), {
                    maxWidth: 300,
                    className: 'custom-popup'
                });

                return marker;
            } catch (error) {
                console.error('Failed to build marker', location, error);
                return null;
            }
        }

        function createPopupContent(location) {
            let content = '<div class="popup-content">';
            content += '<div class="popup-title">' + location.name_ar + '</div>';

            if (location.address_ar) {
                content += '<div class="popup-address"><i class="fas fa-map-marker-alt"></i> ' + location.address_ar + '</div>';
            }

            content += '<div class="popup-badges">';
            if (location.governorate) {
                content += '<span class="popup-badge" style="background-color: #6c757d">' + location.governorate.name_ar + '</span>';
            }
            if (location.tourism_type) {
                content += '<span class="popup-badge" style="background-color: ' + location.tourism_type.color + '">' + location.tourism_type.name_ar + '</span>';
            }
            content += '</div>';

            if (location.categories && location.categories.length > 0) {
                content += '<div class="popup-badges">';
                location.categories.forEach(function (category) {
                    content += '<span class="popup-badge" style="background-color: ' + category.color + '">' + category.name_ar + '</span>';
                });
                content += '</div>';
            }

            content += '<div class="popup-actions">';
            content += '<a href="' + investorLocationBaseUrl + '/' + location.id + '" class="popup-btn">';
            content += '<i class="fas fa-eye"></i> عرض التفاصيل';
            content += '</a>';
            content += '</div>';
            content += '</div>';

            return content;
        }

        function filterLocations() {
            const searchTerm = document.getElementById('location-search').value.toLowerCase();
            const activeTourismFilter = document.querySelector('#tourism-filters .filter-btn.active')?.dataset.filter;
            const activeGovernorateFilter = document.querySelector('#governorate-filters .filter-btn.active')?.dataset.filter;
            let visibleCount = 0;

            markersGroup.clearLayers();

            allMarkers.forEach(function (item) {
                const location = item.data;
                let shouldShow = true;

                if (
                    searchTerm &&
                    !location.name_ar.toLowerCase().includes(searchTerm) &&
                    !(location.address_ar && location.address_ar.toLowerCase().includes(searchTerm))
                ) {
                    shouldShow = false;
                }

                if (activeTourismFilter && activeTourismFilter !== 'all') {
                    const tourismId = activeTourismFilter.replace('tourism-', '');
                    if (!location.tourism_type || String(location.tourism_type.id) !== tourismId) {
                        shouldShow = false;
                    }
                }

                if (activeGovernorateFilter && activeGovernorateFilter !== 'all') {
                    const govId = activeGovernorateFilter.replace('gov-', '');
                    if (!location.governorate || String(location.governorate.id) !== govId) {
                        shouldShow = false;
                    }
                }

                if (shouldShow) {
                    markersGroup.addLayer(item.marker);
                    visibleCount++;
                }
            });

            document.getElementById('visible-locations').textContent = visibleCount;

            if (visibleCount > 0) {
                setTimeout(function () {
                    if (markersGroup.getBounds().isValid()) {
                        map.fitBounds(markersGroup.getBounds(), { padding: [20, 20] });
                    }
                }, 100);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            initMap();

            document.getElementById('location-search').addEventListener('input', filterLocations);

            document.querySelectorAll('#tourism-filters .filter-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('#tourism-filters .filter-btn').forEach(function (button) {
                        button.classList.remove('active');
                    });
                    btn.classList.add('active');
                    filterLocations();
                });
            });

            document.querySelectorAll('#governorate-filters .filter-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('#governorate-filters .filter-btn').forEach(function (button) {
                        button.classList.remove('active');
                    });
                    btn.classList.add('active');
                    filterLocations();
                });
            });

            document.getElementById('reset-filters').addEventListener('click', function () {
                document.getElementById('location-search').value = '';
                document.querySelectorAll('.filter-btn').forEach(function (btn) {
                    btn.classList.remove('active');
                });
                document.querySelectorAll('.filter-btn[data-filter="all"]').forEach(function (btn) {
                    btn.classList.add('active');
                });
                filterLocations();
            });

            document.getElementById('reset-view').addEventListener('click', function () {
                if (originalBounds && originalBounds.isValid()) {
                    map.fitBounds(originalBounds, { padding: [20, 20] });
                } else {
                    map.setView([34.802075, 38.996815], 7);
                }
            });
        });
    </script>
@endpush
