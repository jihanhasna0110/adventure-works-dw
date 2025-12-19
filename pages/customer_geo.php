<?php
$page_title = "Customer Geography";
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Customer Geography Dashboard</h1>
    <div>
        <select id="yearFilterGeo" class="form-control form-control-sm d-inline-block" style="width: auto;">
            <!-- PENTING: Default value harus tahun spesifik, bukan "all" -->
            <option value="2004">2004</option>
            <option value="2003">2003</option>
            <option value="2002">2002</option>
            <option value="2001">2001</option>
            <option value="all" selected>All Years</option>
        </select>
    </div>
</div>

<!-- Summary Cards -->
<div class="row">

    <!-- Total Countries Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Countries Served</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCountries">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-globe fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total States Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            States/Provinces</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalStates">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Cities Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Cities Active
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCities">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-city fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Customers Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Customers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCustomersGeo">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ROW 1: Wilayah + Top Cities (SAMA TINGGI) -->
<div class="row">
    <!-- Wilayah mana yang memberikan kontribusi penjualan terbesar? -->
    <div class="col-xl-8 col-lg-8">
        <div class="card shadow mb-4" style="height: 360px;">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="height: 55px;">
                <h6 class="m-0 font-weight-bold text-primary" style="font-size: 0.9rem; line-height: 1.1;">Wilayah mana yang memberikan kontribusi penjualan terbesar?</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                        <a class="dropdown-item" href="#" onclick="changeGeoChartType('bar')">Bar Chart</a>
                        <a class="dropdown-item" href="#" onclick="changeGeoChartType('pie')">Pie Chart</a>
                        <a class="dropdown-item" href="#" onclick="changeGeoChartType('doughnut')">Doughnut Chart</a>
                    </div>
                </div>
            </div>
            <div class="card-body p-2" style="height: calc(100% - 55px); padding: 10px;">
                <div style="position: relative; height: 90%; max-height: 280px;">
                    <canvas id="geoSalesChart" style="width: 100% !important; height: 100% !important;"></canvas>
                </div>
            </div>
        </div>
    </div>


    <!-- Top 10 Cities by Sales -->
    <div class="col-xl-4 col-lg-4">
        <div class="card shadow mb-4" style="height: 360px;">
            <div class="card-header py-3" style="height: 55px;">
                <h6 class="m-0 font-weight-bold text-success" style="font-size: 0.9rem; line-height: 1.1;">
                    <i class="fas fa-trophy text-warning mr-1"></i>Top 10 Cities by Sales
                </h6>
            </div>
            <div class="card-body p-3" style="height: calc(100% - 55px); overflow-y: auto;">
                <div id="topCitiesList">
                    <div class="text-center text-muted p-3">
                        <i class="fas fa-spinner fa-spin fa-lg mb-2 d-block"></i>Loading...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ROW 2: Country + States (SAMA TINGGI) -->
<div class="row">
    <!-- Sales Distribution by Country -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4" style="height: 360px;">
            <div class="card-header py-3" style="height: 55px;">
                <h6 class="m-0 font-weight-bold text-primary" style="font-size: 0.9rem; line-height: 1.1;">Sales Distribution by Country</h6>
            </div>
            <div class="card-body p-0" style="height: calc(100% - 55px);">
                <div style="position: relative; height: 100%;">
                    <canvas id="countryChart" style="width: 100% !important; height: 100% !important;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 10 States/Provinces by Sales -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4" style="height: 360px;">
            <div class="card-header py-3" style="height: 55px;">
                <h6 class="m-0 font-weight-bold text-primary" style="font-size: 0.9rem; line-height: 1.1;">Top 10 States/Provinces by Sales</h6>
            </div>
            <div class="card-body p-0" style="height: calc(100% - 55px);">
                <div style="position: relative; height: 100%;">
                    <canvas id="stateChart" style="width: 100% !important; height: 100% !important;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Regional Performance Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table"></i> Regional Sales Performance Details
                </h6>
                <div>
                    <button class="btn btn-sm btn-success" onclick="exportGeoData()">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                    <button class="btn btn-sm btn-info" onclick="refreshGeoData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="geoTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>Country</th>
                                <th>State/Province</th>
                                <th>City</th>
                                <th>Total Sales</th>
                                <th>Customers</th>
                                <th>Avg per Customer</th>
                                <th>Market Share %</th>
                            </tr>
                        </thead>
                        <tbody id="geoTableBody">
                            <tr>
                                <td colspan="7" class="text-center">
                                    <i class="fas fa-spinner fa-spin"></i> Loading data...
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <th colspan="3" class="text-right">TOTAL:</th>
                                <th id="footerTotalSales">$0</th>
                                <th id="footerTotalCustomers">0</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Drill-Down Modal for City Details -->
<div class="modal fade" id="cityDetailModal" tabindex="-1" role="dialog" aria-labelledby="cityDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="cityDetailModalLabel">
                    <i class="fas fa-city mr-2"></i>City Sales Detail
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="cityDetailContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<!-- Custom JavaScript -->
<script src="../assets/js/customer_geo.js?v=20251211_999"></script>