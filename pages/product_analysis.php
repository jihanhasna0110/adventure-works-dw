<?php
$page_title = "Product Analysis";
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<!-- Page Heading -->

<!-- Pastikan dropdown year default adalah tahun spesifik, bukan "all" -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Product Analysis Dashboard</h1>
    <div>
        <select id="yearFilterProduct" class="form-control form-control-sm d-inline-block" style="width: auto;">
            <!-- PENTING: Default value harus tahun spesifik, bukan "all" -->
            <option value="2004">2004</option>
            <option value="2003">2003</option>
            <option value="2002">2002</option>
            <option value="2001">2001</option>
            <option value="all" selected>All Years</option>
        </select>
    </div>
</div>

<!-- Content Row: Trend & Top 5 -->
<div class="row align-items-stretch">
    <!-- Business Question 3: Product Performance -->
    <div class="col-xl-8 col-lg-7 col-md-12 d-flex">
        <div class="card shadow mb-4 flex-fill">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Produk mana yang memiliki tren penjualan menurun?
                </h6>
            </div>
            <div class="card-body">
                <!-- HAPUS loading overlay - pakai dashboard pattern -->
                <div class="chart-area" style="height: 320px;">
                    <canvas id="productTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products Widget -->
    <div class="col-xl-4 col-lg-5 col-md-12 d-flex">
        <div class="card shadow mb-4 flex-fill">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    Top 5 Best Selling Products
                </h6>
            </div>
            <div class="card-body">
                <!-- HAPUS loading overlay - pakai dashboard pattern -->
                <div id="topProductsList">
                    <!-- Initial loading state dari JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row: Category & Mix -->
<div class="row align-items-stretch">
    <!-- Business Question 4: Category Comparison -->
    <div class="col-xl-6 col-lg-6 col-md-12 d-flex">
        <div class="card shadow mb-4 flex-fill">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Perbandingan performa antar kategori produk?
                </h6>
            </div>
            <div class="card-body">
                <!-- HAPUS loading overlay - pakai dashboard pattern -->
                <div style="position: relative; height: 320px; width: 100%;">
                    <canvas id="categoryComparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Mix Analysis -->
    <div class="col-xl-6 col-lg-6 col-md-12 d-flex">
        <div class="card shadow mb-4 flex-fill">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Product Mix by Quantity vs Revenue
                </h6>
            </div>
            <div class="card-body">
                <!-- HAPUS loading overlay - pakai dashboard pattern -->
                <div style="position: relative; height: 320px; width: 100%;">
                    <canvas id="productMixChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Details Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    Product Performance Details
                </h6>
                <div>
                    <button class="btn btn-sm btn-success" onclick="exportTableData()">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- HAPUS loading overlay - pakai dashboard pattern -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="productAnalysisTable" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Sales Amount</th>
                                <th>Order Qty</th>
                                <th>Avg Price</th>
                                <th>Growth %</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="productAnalysisBody">
                            <!-- Initial loading state dari JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<!-- Custom CSS (HAPUS .loading-overlay, TINGGAL rank-badge saja) -->
<style>
    .rank-badge {
        min-width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        border-radius: 50%;
        padding: 0;
    }

    /* Dashboard-style chart loading (opsional, bisa dihapus kalau mau minimalis) */
    .chart-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
        background: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 10px;
    }

    #topProductsList .list-group-item {
        border-color: rgba(0, 0, 0, 0.05);
    }

    #topProductsList .badge.rounded-circle {
        padding: 0;
    }
</style>

<!-- Custom JavaScript -->
<script src="../assets/js/product_analysis.js?v=20251211"></script>