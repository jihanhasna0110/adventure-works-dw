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
            <option value="2006">2006</option>
            <option value="2007">2007</option>
            <option value="2008" selected>2008</option>
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

<!-- Content Row -->
<div class="row">
    
    <!-- Business Question 5: Geographic Distribution -->
    <div class="col-xl-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    Wilayah mana yang memberikan kontribusi penjualan terbesar?
                </h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Chart Type:</div>
                        <a class="dropdown-item" href="#" onclick="changeGeoChartType('bar')">Bar Chart</a>
                        <a class="dropdown-item" href="#" onclick="changeGeoChartType('pie')">Pie Chart</a>
                        <a class="dropdown-item" href="#" onclick="changeGeoChartType('doughnut')">Doughnut Chart</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="geoSalesChart" height="60"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Top Regions Widget -->
    <div class="col-xl-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-trophy text-warning"></i> Top 10 Cities by Sales
                </h6>
            </div>
            <div class="card-body" style="max-height: 420px; overflow-y: auto;">
                <div id="topCitiesList">
                    <div class="text-center text-muted">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- Content Row - Country & State Charts -->
<div class="row">
    
    <!-- Sales by Country -->
    <div class="col-xl-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Sales Distribution by Country
                </h6>
            </div>
            <div class="card-body">
                <canvas id="countryChart" height="80"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Sales by State (Top 10) -->
    <div class="col-xl-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Top 10 States/Provinces by Sales
                </h6>
            </div>
            <div class="card-body">
                <canvas id="stateChart" height="80"></canvas>
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

<!-- Custom JavaScript -->
<script src="../assets/js/customer_geo.js"></script>

<?php include '../includes/footer.php'; ?>