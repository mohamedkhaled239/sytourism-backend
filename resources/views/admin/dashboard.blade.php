@extends('admin.layouts.app')

@section('title', 'الرئيسية')

@section('content')
    <div class="row mb-4">
        @if(!$isScopedAdmin)
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <h6 class="mb-3">إجمالي المستخدمين</h6>
                    <h3>{{ $statistics['total_users'] }}</h3>
                    <small>{{ $statistics['total_tourists'] }} سائح | {{ $statistics['total_investors'] }} مستثمر</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <h6 class="mb-3">المستثمرون بانتظار الموافقة</h6>
                    <h3>{{ $statistics['pending_investors'] }}</h3>
                    <small>يتطلبون اعتماد الإدارة</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <h6 class="mb-3">الأخبار</h6>
                    <h3>{{ $statistics['total_news'] }}</h3>
                    <small>خبر منشور</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <h6 class="mb-3">الأحداث</h6>
                    <h3>{{ $statistics['total_events'] }}</h3>
                    <small>حدث مسجل</small>
                </div>
            </div>
        @endif
    </div>

    <div class="row mb-4">
        @if(!$isScopedAdmin)
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <h6 class="mb-3">الاستثمارات</h6>
                    <h3>{{ $statistics['total_investments'] }}</h3>
                    <small>فرصة استثمارية</small>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <h6 class="mb-3">المستثمرون المعتمدون</h6>
                    <h3>{{ $statistics['total_investors'] - $statistics['pending_investors'] }}</h3>
                    <small>مستثمر تمت الموافقة عليه</small>
                </div>
            </div>
        @endif
        <div class="col-md-{{ $isScopedAdmin ? '12' : '4' }} mb-3">
            <div class="stat-card">
                <h6 class="mb-3">المواقع السياحية</h6>
                <h3>{{ $statistics['total_locations'] }}</h3>
                <small>{{ $isScopedAdmin ? 'البيانات المعروضة داخل محافظتك فقط' : 'إجمالي المواقع المتاحة' }}</small>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i> إحصائيات المواقع
                        </h5>
                        <a href="{{ route('admin.locations.export') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel me-2"></i> تصدير المواقع
                        </a>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div style="position: relative; height: 350px;">
                                <h6 class="text-center mb-3">المواقع حسب المحافظة</h6>
                                <canvas id="governorateChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div style="position: relative; height: 350px;">
                                <h6 class="text-center mb-3">المواقع حسب التصنيف السياحي</h6>
                                <canvas id="tourismTypeChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div style="position: relative; height: 350px;">
                                <h6 class="text-center mb-3">المواقع حسب النوع</h6>
                                <canvas id="locationTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$isScopedAdmin)
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">آخر المستثمرين</h5>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(\App\Models\User::where('user_type', 'investor')->latest()->take(5)->get() as $user)
                                <tr>
                                    <td>{{ $user->full_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_approved ? 'success' : 'warning' }}">
                                            {{ $user->is_approved ? 'معتمد' : 'بانتظار الموافقة' }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">آخر الأخبار</h5>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>المشاهدات</th>
                                <th>التاريخ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(\App\Models\News::latest()->take(5)->get() as $news)
                                <tr>
                                    <td>{{ \Illuminate\Support\Str::limit($news->title_ar, 30) }}</td>
                                    <td>{{ $news->views }}</td>
                                    <td>{{ $news->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Cairo', sans-serif";

            const governorateData = @json($locationsByGovernorate);
            const tourismTypeData = @json($locationsByTourismType);
            const locationTypeData = @json($locationsByType);

            function renderChart(elementId, type, labels, values, color) {
                const canvas = document.getElementById(elementId);
                if (!canvas || labels.length === 0) {
                    return;
                }

                new Chart(canvas.getContext('2d'), {
                    type: type,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'عدد المواقع',
                            data: values,
                            backgroundColor: color,
                            borderColor: color,
                            borderWidth: 2,
                            borderRadius: 8,
                            fill: type === 'line',
                            tension: 0.35
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            }

            renderChart('governorateChart', 'bar', Object.keys(governorateData), Object.values(governorateData), '#3E5828');
            renderChart('tourismTypeChart', 'line', Object.keys(tourismTypeData), Object.values(tourismTypeData), '#8A6B4E');
            renderChart('locationTypeChart', 'bar', Object.keys(locationTypeData), Object.values(locationTypeData), '#6f8c5a');
        });
    </script>
@endpush
