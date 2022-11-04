<x-admin-layout title="Dashboard">
    <x-slot name="subHeader">
        <x-admin.sub-header headerTitle="Dashboard">
            <x-slot name="toolbar"></x-slot>
        </x-admin.sub-header>
    </x-slot>
    <div class="kt-portlet">
        <div class="kt-portlet__body  kt-portlet__body--fit">
            <div class="row row-no-padding row-col-separator-xl">
                <div class="col-md-12 col-lg-6 col-xl-4">
                    <div class="kt-widget24">
                        <div class="kt-widget24__details">
                            <div class="kt-widget24__info">
                                <h4 class="kt-widget24__title">
                                    Total Users
                                </h4>
                                <span class="kt-widget24__desc">
                                    Total user available in this system
                                </span>
                            </div>
                            <span class="kt-widget24__stats kt-font-brand">
                                <a href="{{ route('users.index') }}">{{ $count['userCount'] }}</a>
                            </span>
                        </div>
                        <div class="progress progress--sm">
                            <div class="progress-bar kt-bg-brand" role="progressbar"
                                style="width: {{ $count['userCount'] }}%;" aria-valuenow="50" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                        <div class="kt-widget24__action">
                            <a class="kt-widget24__change" href="{{ route('users.index') }}">
                                View
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6 col-xl-4">
                    <div class="kt-widget24">
                        <div class="kt-widget24__details">
                            <div class="kt-widget24__info">
                                <h4 class="kt-widget24__title">
                                    Total Inactive Users
                                </h4>
                                <span class="kt-widget24__desc">
                                    Total inactive user available in this system
                                </span>
                            </div>
                            <span class="kt-widget24__stats kt-font-warning">
                                <a
                                    href="{{ route('users.index', ['status' => 'inactive']) }}">{{ $count['blockedUserCount'] }}</a>
                            </span>
                        </div>
                        <div class="progress progress--sm">
                            <div class="progress-bar kt-bg-warning" role="progressbar"
                                style="width: {{ $count['blockedUserCount'] }}%;" aria-valuenow="50" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                        <div class="kt-widget24__action">
                            <a class="kt-widget24__change" href="{{ route('users.index', ['status' => 'inactive']) }}">
                                View
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6 col-xl-4">
                    <div class="kt-widget24">
                        <div class="kt-widget24__details">
                            <div class="kt-widget24__info">
                                <h4 class="kt-widget24__title">
                                    Total Active Users
                                </h4>
                                <span class="kt-widget24__desc">
                                    Total active user available in this system
                                </span>
                            </div>
                            <span class="kt-widget24__stats kt-font-danger">
                                <a
                                    href="{{ route('users.index', ['status' => 'active']) }}">{{ $count['activeUserCount'] }}</a>
                            </span>
                        </div>
                        <div class="progress progress--sm">
                            <div class="progress-bar kt-bg-danger" role="progressbar"
                                style="width: {{ $count['activeUserCount'] }}%;" aria-valuenow="50" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                        <div class="kt-widget24__action">
                            <a class="kt-widget24__change" href="{{ route('users.index', ['status' => 'active']) }}">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="kt-portlet kt-portlet--height-fluid ">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Analytics
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body kt-portlet__body--fluid kt-portlet__body--fit">
                    <div class="kt-widget4 kt-widget4--sticky">
                        <div class="kt-widget4__items kt-portlet__space-x kt-margin-t-15">
                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="flaticon2-graphic  kt-font-brand"></i>
                                </span>
                                <a href="#" class="kt-widget4__title">
                                    Invites Sent
                                </a>
                                <span class="kt-widget4__number kt-font-brand">30</span>
                            </div>
                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="flaticon2-analytics-2  kt-font-success"></i>
                                </span>
                                <a href="#" class="kt-widget4__title">
                                    Invites Accepted
                                </a>
                                <span class="kt-widget4__number kt-font-success">25</span>
                            </div>
                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="flaticon2-drop  kt-font-danger"></i>
                                </span>
                                <a href="#" class="kt-widget4__title">
                                    Invites Resulting In A Match
                                </a>
                                <span class="kt-widget4__number kt-font-danger">24</span>
                            </div>
                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="flaticon2-pie-chart-4 kt-font-warning"></i>
                                </span>
                                <a href="#" class="kt-widget4__title">
                                    Feedback Received
                                </a>
                                <span class="kt-widget4__number kt-font-warning">35</span>
                            </div>
                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="flaticon2-analytics-2  kt-font-success"></i>
                                </span>
                                <a href="#" class="kt-widget4__title">
                                    Feedback Approved
                                </a>
                                <span class="kt-widget4__number kt-font-success">20</span>
                            </div>
                        </div>
                        <div class="kt-widget4__chart kt-margin-t-15">
                            <!-- <canvas id="kt_chart_latest_updates" style="height: 150px;"></canvas> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="kt-portlet kt-portlet--height-fluid ">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Cohorts
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body kt-portlet__body--fluid kt-portlet__body--fit">
                    <div class="kt-widget4 kt-widget4--sticky">
                        <div class="kt-widget4__items kt-portlet__space-x kt-margin-t-15">
                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="flaticon2-graphic  kt-font-brand"></i>
                                </span>
                                <a href="#" class="kt-widget4__title">
                                    Invites Sent
                                </a>
                                <span class="kt-widget4__number kt-font-brand">30</span>
                            </div>
                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="flaticon2-analytics-2  kt-font-success"></i>
                                </span>
                                <a href="#" class="kt-widget4__title">
                                    Invites Accepted
                                </a>
                                <span class="kt-widget4__number kt-font-success">25</span>
                            </div>
                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="flaticon2-drop  kt-font-danger"></i>
                                </span>
                                <a href="#" class="kt-widget4__title">
                                    Invites Resulting In A Match
                                </a>
                                <span class="kt-widget4__number kt-font-danger">24</span>
                            </div>
                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="flaticon2-pie-chart-4 kt-font-warning"></i>
                                </span>
                                <a href="#" class="kt-widget4__title">
                                    Feedback Received
                                </a>
                                <span class="kt-widget4__number kt-font-warning">35</span>
                            </div>
                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="flaticon2-analytics-2  kt-font-success"></i>
                                </span>
                                <a href="#" class="kt-widget4__title">
                                    Feedback Approved
                                </a>
                                <span class="kt-widget4__number kt-font-success">20</span>
                            </div>
                        </div>
                        <div class="kt-widget4__chart kt-margin-t-15">
                            <!-- <canvas id="kt_chart_latest_updates" style="height: 150px;"></canvas> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-lg-6 col-xl-6">
            <div class="kt-portlet">
                <canvas id="myChart" style="width:100%;max-width:600px" width="703" height="351"></canvas>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6">
            <div class="kt-portlet">
                <canvas id="myChart2" style="width:100%;max-width:600px" width="703" height="351"></canvas>
            </div>
        </div>
    </div>
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Visitors Over The Last 7 Days
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="chart-div-parent position-relative">
                <div id="chartDivSiteVisit"></div>
                <div class="col-chart position-absolute bg-white"
                    style="bottom:0; left:0; z-index:9; height: 22px; width: 70px;"></div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
    @push('scripts')
        <script>
            var xValues = ['Event', 'Entertainment', 'Games', 'Outdoor'];
            var yValues = [1, 3, 2, 3];
            var barColors = [
                "#b91d47",
                "#00aba9",
                "#2b5797",
                "#2b5707",
            ];

            new Chart("myChart", {
                type: "doughnut",
                data: {
                    labels: xValues,
                    datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                    }]
                },
                options: {
                    title: {
                        display: true,
                        text: "Category Statistics"
                    }
                }
            });

            var kValues = ["1 star", "2 star", "3 star", "4 star", "5 star"];
            var lValues = ["10", "12", "11", "14", "17"];
            var barColors = ["brown", "green", "blue", "red", "purple"];

            new Chart("myChart2", {
                type: "bar",
                data: {
                    labels: kValues,
                    datasets: [{
                        backgroundColor: barColors,
                        data: lValues
                    }]
                },
                options: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: "Feedback Statistics"
                    }
                }
            });

            //Last 7 days visitor chart
            am4core.ready(function() {
                am4core.useTheme(am4themes_animated);
                var chart = am4core.create("chartDivSiteVisit", am4charts.XYChart3D);
                var visited_7_days = "{{ $visit_last_days }}";
                visited_7_days = JSON.parse(visited_7_days.replace(/(&quot\;)/g, "\""));
                $.each(visited_7_days, function(key, value) {
                    value.color = chart.colors.next();
                });
                chart.data = visited_7_days;
                // Create axes
                var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "date";
                categoryAxis.numberFormatter.numberFormat = "#";
                categoryAxis.renderer.inversed = true;
                var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
                valueAxis.min = 0;
                // Create series
                var series = chart.series.push(new am4charts.ColumnSeries3D());
                series.dataFields.valueX = "count_visitor";
                series.dataFields.categoryY = "date";
                series.name = "Count Visitor";
                series.columns.template.propertyFields.fill = "color";
                series.columns.template.tooltipText = "{valueX}";
                series.columns.template.column3D.stroke = am4core.color("#fff");
                series.columns.template.column3D.strokeOpacity = 0.2;
            });

            function getProfitLossResult(obj) {
                var year = $('#year1').val();
                var month = $('#month').val();
                var query = $("#searchData").val();
                var url = ADMIN_BASE_URL + '/dashboard?query=' + query;
                window.location.href = url;
            }
        </script>
    @endpush

</x-admin-layout>
