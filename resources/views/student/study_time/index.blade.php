@extends('layouts.student')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="fw-bold text-dark">My Study Time</h2>
        <p class="text-muted">Track your daily learning progress and activity.</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card border-0 shadow-sm h-100 rounded-4 bg-primary text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title fw-bold mb-0">Today's Activity</h5>
                    <i class="bi bi-calendar-day fs-3 opacity-50"></i>
                </div>
                <h1 class="display-4 fw-bold mb-0">
                    {{ floor($todaySeconds / 3600) }}<span class="fs-4">h</span> 
                    {{ floor(($todaySeconds % 3600) / 60) }}<span class="fs-4">m</span>
                </h1>
                <p class="mt-2 mb-0 opacity-75 small">Keep up the momentum!</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card border-0 shadow-sm h-100 rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title fw-bold text-muted mb-0">Total Learning Time</h5>
                    <i class="bi bi-hourglass-split fs-3 text-warning"></i>
                </div>
                <h2 class="fw-bold mb-0 text-dark">
                    {{ floor($totalSeconds / 3600) }}<span class="fs-5 text-muted">h</span> 
                    {{ floor(($totalSeconds % 3600) / 60) }}<span class="fs-5 text-muted">m</span>
                </h2>
                <p class="text-muted mt-2 mb-0 small">Since you joined</p>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-4">Activity (Last 7 Days)</h5>
                <div id="studyChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- ApexCharts CDM -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [{
                name: 'Study Hours',
                data: {!! json_encode($chartData['hours']) !!}
            }],
            chart: {
                type: 'bar',
                height: 350,
                fontFamily: 'Outfit, sans-serif',
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    columnWidth: '40%',
                }
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 0, colors: ['transparent'] },
            xaxis: {
                categories: {!! json_encode($chartData['dates']) !!},
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                title: { text: 'Hours' }
            },
            fill: {
                opacity: 1,
                colors: ['#7638ff']
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " hours"
                    }
                }
            },
            grid: {
                borderColor: '#f1f1f1',
            }
        };

        var chart = new ApexCharts(document.querySelector("#studyChart"), options);
        chart.render();
    });
</script>
@endsection
