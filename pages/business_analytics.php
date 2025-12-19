<?php
$page_title = "Business Analytics Dashboard";
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Business Analytics Dashboard</h1>
    <div>
        <select id="yearFilterAnalytics" class="form-control form-control-sm d-inline-block" style="width: auto;">
            <option value="all" selected>All Years</option>
            <option value="2004">2004</option>
            <option value="2003">2003</option>
            <option value="2002">2002</option>
            <option value="2001">2001</option>
        </select>
    </div>
</div>

<!-- ROW 1: 4 Business Questions Cards - WITH LOADING STATES -->
<div class="row">
    <!-- Q1: Card Type -->
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Top Card Type
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="topCardType">Loading...</div>
                        <div class="text-xs text-muted" id="topCardTypeCount">0 tx</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Q2: Top Color -->
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Top Color
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="topColor">Loading...</div>
                        <div class="text-xs text-muted" id="topColorQty">0 units</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-palette fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Q3: AOV Individual -->
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            AOV Individual
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="aovIndividual">$0.00</div>
                        <div class="text-xs text-muted">Per Customer</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Q4: Top Salesperson -->
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Top Salesperson
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-truncate" id="topSalesperson">Loading...</div>
                        <div class="text-xs text-muted" id="topSalesAmount">$0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ROW 2: Charts -->
<div class="row">
    <!-- Q1: Card Type Distribution -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">1. Top Card Types</h6>
                <span class="badge badge-primary">Distribution</span>
            </div>
            <div class="card-body" style="height: 320px;">
                <div class="text-center pt-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-muted mb-3 d-block"></i>
                    <div class="text-muted">Loading chart...</div>
                </div>
                <canvas id="cardTypeChart" style="display: none;"></canvas>
            </div>
        </div>
    </div>

    <!-- Q2: Color Preferences -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-success">2. Color Preferences</h6>
                <span class="badge badge-success">By Units</span>
            </div>
            <div class="card-body" style="height: 320px;">
                <div class="text-center pt-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-muted mb-3 d-block"></i>
                    <div class="text-muted">Loading chart...</div>
                </div>
                <canvas id="colorChart" style="display: none;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ROW 3: AOV by Customer Type + Salesperson Performance -->
<div class="row">
    <!-- Q4: AOV by Customer Type -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-info">4. Average Order Value</h6>
                <span class="badge badge-info">By Customer Type</span>
            </div>
            <div class="card-body" style="height: 320px;">
                <div class="text-center pt-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-muted mb-3 d-block"></i>
                    <div class="text-muted">Loading chart...</div>
                </div>
                <canvas id="aovChart" style="display: none;"></canvas>
            </div>
        </div>
    </div>

    <!-- Q5: Salesperson vs Territory -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-warning">5. Salesperson Performance</h6>
                <span class="badge badge-warning">By Territory</span>
            </div>
            <div class="card-body" style="height: 320px;">
                <div class="text-center pt-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-muted mb-3 d-block"></i>
                    <div class="text-muted">Loading chart...</div>
                </div>
                <canvas id="salespersonChart" style="display: none;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Tables -->
<div class="row">
    <div class="col-xl-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Detailed Analytics Data</h6>
                <div>
                    <button class="btn btn-sm btn-success" onclick="refreshAnalyticsData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="analyticsTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="card-tab" data-toggle="tab" href="#cardData" role="tab">Card Types</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="color-tab" data-toggle="tab" href="#colorData" role="tab">Colors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="status-tab" data-toggle="tab" href="#statusData" role="tab">Order Status</a>
                    </li>
                </ul>
                <div class="tab-content" id="analyticsTabContent">
                    <!-- Card Types -->
                    <div class="tab-pane fade show active" id="cardData" role="tabpanel">
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover" id="cardTable" width="100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="sortable" data-col="0">Card Type</th>
                                        <th class="sortable text-right" data-col="1">Transactions</th>
                                        <th class="sortable text-right" data-col="2">Amount</th>
                                        <th class="sortable text-right" data-col="3">% Total</th>
                                    </tr>
                                </thead>
                                <tbody id="cardTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <i class="fas fa-spinner fa-spin fa-lg mb-2 d-block"></i> Loading...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Colors -->
                    <div class="tab-pane fade" id="colorData" role="tabpanel">
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover" id="colorTable" width="100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="sortable" data-col="0">Color</th>
                                        <th class="sortable text-right" data-col="1">Quantity</th>
                                        <th class="sortable text-right" data-col="2">Products</th>
                                    </tr>
                                </thead>
                                <tbody id="colorTableBody">
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <i class="fas fa-spinner fa-spin fa-lg mb-2 d-block"></i> Loading...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Order Status -->
                    <div class="tab-pane fade" id="statusData" role="tabpanel">
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover" id="statusTable" width="100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="sortable" data-col="0">Status</th>
                                        <th class="sortable text-right" data-col="1">Count</th>
                                        <th class="sortable text-right" data-col="2">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="statusTableBody">
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <i class="fas fa-spinner fa-spin fa-lg mb-2 d-block"></i> Loading...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Business Analytics -->
<style>
/* ===== BUSINESS ANALYTICS CARDS - REFINED STYLING ===== */

/* Stats Cards - Consistent Height & Spacing */
.card.border-left-primary,
.card.border-left-success,
.card.border-left-info,
.card.border-left-warning {
    border-left-width: 0.3rem !important;
    min-height: 100px;
}

/* Card Body Padding - More Compact */
.card.border-left-primary .card-body,
.card.border-left-success .card-body,
.card.border-left-info .card-body,
.card.border-left-warning .card-body {
    padding: 1rem 1.25rem !important;
}

/* Stats Title - Smaller Font */
.card-body .text-xs {
    font-size: 0.7rem;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

/* Main Value - Consistent Size */
.card-body .h5 {
    font-size: 1.4rem;
    margin-bottom: 0.25rem;
    line-height: 1.2;
}

/* Secondary Text */
.card-body .text-muted {
    font-size: 0.75rem;
    color: #858796 !important;
}

/* Icon Sizing */
.card-body .fa-2x {
    font-size: 1.75rem;
    opacity: 0.4;
}

/* Chart Cards - Equal Height & Hover Effect */
.card.shadow {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card.shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}

/* Chart Headers */
.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.card-header .badge {
    font-size: 0.65rem;
    padding: 0.35em 0.65em;
    font-weight: 600;
}

/* Chart Container */
.card-body canvas {
    max-height: 100%;
}

/* Text Truncation for Long Names */
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Color Dot for Table */
.color-dot {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 8px;
    border: 1px solid #ddd;
    vertical-align: middle;
}

/* Sortable Table Headers */
.sortable {
    cursor: pointer;
    user-select: none;
    position: relative;
    transition: background-color 0.2s ease;
}

.sortable:hover {
    background-color: #f1f1f1;
}

.sortable::after {
    content: " ↕";
    opacity: 0.3;
    font-size: 0.8em;
    margin-left: 5px;
}

.sortable.sorted-asc::after {
    content: " ↑";
    opacity: 1;
    color: #4e73df;
}

.sortable.sorted-desc::after {
    content: " ↓";
    opacity: 1;
    color: #4e73df;
}

/* Table Styling */
.table-hover tbody tr:hover {
    background-color: #f8f9fc;
}

.table thead th {
    border-bottom: 2px solid #e3e6f0;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.75rem;
    color: #858796;
}

/* Badge Styling in Tables */
.badge-info {
    background-color: #36b9cc;
    padding: 0.35em 0.65em;
}

/* Loading State */
.text-center i.fa-spinner {
    color: #858796;
    font-size: 1.5rem;
}

/* Toast Alert */
.toast-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 5px;
    z-index: 9999;
    font-weight: 600;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Tab Styling */
.nav-tabs .nav-link {
    border: none;
    color: #858796;
    font-weight: 600;
    padding: 0.75rem 1rem;
}

.nav-tabs .nav-link.active {
    color: #4e73df;
    border-bottom: 3px solid #4e73df;
    background-color: transparent;
}

.nav-tabs .nav-link:hover {
    border-color: transparent;
    color: #4e73df;
}

/* Responsive Adjustments */
@media (max-width: 1199px) {
    .col-xl-3 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (max-width: 767px) {
    .col-xl-3, .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .card-body .h5 {
        font-size: 1.2rem;
    }
    
    .card-body .fa-2x {
        font-size: 1.5rem;
    }
    
    .card-body {
        padding: 0.75rem 1rem !important;
    }
    
    .card-body[style*="height"] {
        height: 280px !important;
    }
}

@media (max-width: 576px) {
    .card-header h6 {
        font-size: 0.85rem;
    }
    
    .card-header .badge {
        font-size: 0.6rem;
        padding: 0.25em 0.5em;
    }
}
</style>

<?php include '../includes/footer.php'; ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="../assets/js/business_analytics.js?v=<?= time() ?>"></script>
