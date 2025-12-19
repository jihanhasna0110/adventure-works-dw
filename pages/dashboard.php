<?php
$page_title = "Sales Overview";
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<style>
#drillDownModal .modal-body {
  max-height: 70vh;
  overflow-y: auto;
}
</style>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading + Filter -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Sales Overview Dashboard</h1>
            <small class="text-muted">Adventure Works DWH</small>
        </div>
        <div class="d-flex align-items-center">
            <!-- Year filter yang dipakai dashboard.js -->
            <select id="yearFilter" class="form-control form-control-sm mr-2">
                <option value="all" selected>All Years</option>
                <option value="2004">2004</option>
                <option value="2003">2003</option>
                <option value="2002">2002</option>
                <option value="2001">2001</option>
            </select>

            <button id="resetFilter" class="btn btn-sm btn-secondary mr-2">
                <i class="fas fa-undo-alt fa-sm"></i> Reset Filter
            </button>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </a>
        </div>
    </div>

    <!-- Content Row - KPI Cards -->
    <div class="row">

        <!-- Total Sales Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sales (YTD)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSales">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalOrders">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg Order Value Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Avg Order Value
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgOrder">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Growth Rate Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Growth Rate (YoY)
                            </div>
                            <div class="h5 mb-0 font-weight-bold" id="growthRate">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Content Row - Charts -->
    <div class="row">

        <!-- Sales Trend Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Bagaimana tren penjualan per bulan dalam setahun terakhir?
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="#" onclick="exportData('csv')">Export Data</a>
                        </div>
                    </div>
                </div>
                <div class="card-body position-relative">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales by Category Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header -->
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sales by Category</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <!-- ✅ CONTAINER DENGAN TINGGI TETAP -->
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Content Row - Product Table -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Detail Penjualan Per Produk
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Sales Amount</th>
                                    <th>Order Qty</th>
                                    <th>Growth %</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody">
                                <!-- Diisi via loadProductTable() -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- End of container-fluid -->

<!-- Drill-Down Modal -->
<div class="modal fade" id="drillDownModal" tabindex="-1" role="dialog"
    aria-labelledby="drillDownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="drillDownTitle">
                    <i class="fas fa-chart-bar mr-2"></i>Detailed Analysis
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="drillDownContent">
                <!-- Diisi oleh drillDownToMonth() -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="exportData('csv')">
                    <i class="fas fa-download mr-2"></i>Export Data
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// footer biasanya sudah include jQuery, Bootstrap, Chart.js, DataTables
include '../includes/footer.php';
?>

<!-- Pastikan setelah footer, dashboard.js dipanggil -->
<!-- UBAH INI -->
<script src="../assets/js/dashboard.js?v=20251210"></script>