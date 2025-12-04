/**
 * Product Analysis JavaScript
 */

let productTrendChart = null;
let categoryComparisonChart = null;
let productMixChart = null;

$(document).ready(function() {
    loadProductAnalysis();
    
    $('#yearFilterProduct').on('change', function() {
        loadProductAnalysis();
    });
});

function loadProductAnalysis() {
    const year = $('#yearFilterProduct').val();
    
    loadProductTrendChart(year);
    loadCategoryComparison(year);
    loadProductMix(year);
    loadTopProducts(year);
    loadProductTable(year);
}

// Product Trend Chart
function loadProductTrendChart(year) {
    $.ajax({
        url: '../api/get_data.php',
        data: { action: 'sales_trend', year: year },
        dataType: 'json',
        success: function(data) {
            if (productTrendChart) productTrendChart.destroy();
            
            const ctx = document.getElementById('productTrendChart').getContext('2d');
            productTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.month),
                    datasets: [{
                        label: 'Sales Trend',
                        data: data.map(d => d.total_sales),
                        borderColor: 'rgba(231, 74, 59, 1)',
                        backgroundColor: 'rgba(231, 74, 59, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Sales: $' + formatNumber(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + formatNumber(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    });
}

// Category Comparison Chart
function loadCategoryComparison(year) {
    $.ajax({
        url: '../api/get_data.php',
        data: { action: 'sales_by_category', year: year },
        dataType: 'json',
        success: function(data) {
            if (categoryComparisonChart) categoryComparisonChart.destroy();
            
            const ctx = document.getElementById('categoryComparisonChart').getContext('2d');
            categoryComparisonChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.category_name),
                    datasets: [{
                        label: 'Sales by Category',
                        data: data.map(d => d.total_sales),
                        backgroundColor: [
                            'rgba(78, 115, 223, 0.8)',
                            'rgba(28, 200, 138, 0.8)',
                            'rgba(54, 185, 204, 0.8)',
                            'rgba(246, 194, 62, 0.8)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            ticks: {
                                callback: function(value) {
                                    return '$' + formatNumber(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    });
}

// Product Mix Chart
function loadProductMix(year) {
    $.ajax({
        url: '../api/get_data.php',
        data: { action: 'sales_by_category', year: year },
        dataType: 'json',
        success: function(data) {
            if (productMixChart) productMixChart.destroy();
            
            const ctx = document.getElementById('productMixChart').getContext('2d');
            productMixChart = new Chart(ctx, {
                type: 'polarArea',
                data: {
                    labels: data.map(d => d.category_name),
                    datasets: [{
                        data: data.map(d => d.total_sales),
                        backgroundColor: [
                            'rgba(78, 115, 223, 0.6)',
                            'rgba(28, 200, 138, 0.6)',
                            'rgba(54, 185, 204, 0.6)',
                            'rgba(246, 194, 62, 0.6)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    });
}

// Top Products List
function loadTopProducts(year) {
    $.ajax({
        url: '../api/get_data.php',
        data: { action: 'top_products', year: year, limit: 10 },
        dataType: 'json',
        success: function(data) {
            let html = '<div class="list-group list-group-flush">';
            data.forEach(function(item, index) {
                const medal = index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : (index + 1);
                html += '<div class="list-group-item d-flex justify-content-between align-items-center p-2">';
                html += '<div><span class="badge badge-primary mr-2">' + medal + '</span>';
                html += '<small>' + item.product_name + '</small></div>';
                html += '<strong class="text-success">$' + formatNumber(item.sales_amount) + '</strong>';
                html += '</div>';
            });
            html += '</div>';
            $('#topProductsList').html(html);
        }
    });
}

// Product Table
function loadProductTable(year) {
    $.ajax({
        url: '../api/get_data.php',
        data: { action: 'product_sales', year: year },
        dataType: 'json',
        success: function(data) {
            let html = '';
            data.forEach(function(item) {
                const avgPrice = item.sales_amount / item.order_qty;
                const status = item.growth >= 0 ? 
                    '<span class="badge badge-success">Growing</span>' : 
                    '<span class="badge badge-danger">Declining</span>';
                
                html += '<tr>';
                html += '<td>' + item.product_name + '</td>';
                html += '<td><span class="badge badge-info">' + item.category_name + '</span></td>';
                html += '<td class="text-right">$' + formatNumber(item.sales_amount) + '</td>';
                html += '<td class="text-right">' + formatNumber(item.order_qty) + '</td>';
                html += '<td class="text-right">$' + formatNumber(avgPrice) + '</td>';
                html += '<td class="text-right">' + item.growth + '%</td>';
                html += '<td class="text-center">' + status + '</td>';
                html += '</tr>';
            });
            
            $('#productAnalysisBody').html(html);
            
            if ($.fn.DataTable.isDataTable('#productAnalysisTable')) {
                $('#productAnalysisTable').DataTable().destroy();
            }
            $('#productAnalysisTable').DataTable({
                "order": [[2, "desc"]]
            });
        }
    });
}

function formatNumber(num) {
    return parseFloat(num).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}

function exportTableData() {
    alert('Exporting data...');
    // Implement export functionality
}