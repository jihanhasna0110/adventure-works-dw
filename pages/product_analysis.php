<?php 
$page_title = "Product Analysis";
include '../includes/header.php'; 
include '../includes/sidebar.php'; 
include '../includes/topbar.php'; 
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Product Analysis Dashboard</h1>
    <div>
        <select id="yearFilterProduct" class="form-control form-control-sm d-inline-block" style="width: auto;">
            <option value="2006">2006</option>
            <option value="2007">2007</option>
            <option value="2008" selected>2008</option>
        </select>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    
    <!-- Business Question 3: Product Performance -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Produk mana yang memiliki tren penjualan menurun?
                </h6>
            </div>
            <div class="card-body">
                <canvas id="productTrendChart" height="80"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Top Products Widget -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    Top 10 Best Selling Products
                </h6>
            </div>
            <div class="card-body">
                <div id="topProductsList">
                    <!-- Will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- Content Row -->
<div class="row">
    
    <!-- Business Question 4: Category Comparison -->
    <div class="col-xl-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Perbandingan performa antar kategori produk?
                </h6>
            </div>
            <div class="card-body">
                <canvas id="categoryComparisonChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Product Mix Analysis -->
    <div class="col-xl-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Product Mix by Quantity vs Revenue
                </h6>
            </div>
            <div class="card-body">
                <canvas id="productMixChart"></canvas>
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
                            <!-- Data via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom JavaScript -->
<script src="../assets/js/product_analysis.js"></script>

<?php include '../includes/footer.php'; ?>