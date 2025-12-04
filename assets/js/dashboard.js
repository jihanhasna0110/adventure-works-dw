/**
 * Dashboard JavaScript - Adventure Works DWH
 * Handles data loading, chart rendering, and interactivity
 */

// ============================================
// GLOBAL VARIABLES
// ============================================
let salesTrendChart = null;
let categoryChart = null;
let currentFilter = {
    category: null,
    year: 2008
};

// ============================================
// DOCUMENT READY - INITIALIZATION
// ============================================
$(document).ready(function() {
    console.log('Dashboard initializing...');
    
    // Initialize all dashboard components
    initializeDashboard();
});

function initializeDashboard() {
    // Load all dashboard data
    loadDashboardStats();
    loadSalesTrendChart();
    loadCategoryChart();
    loadProductTable();
    
    // Setup event listeners
    setupEventListeners();
}

// ============================================
// EVENT LISTENERS
// ============================================
function setupEventListeners() {
    // Year filter change
    $('#yearFilter').on('change', function() {
        currentFilter.year = $(this).val();
        refreshAllCharts();
    });
    
    // Reset filter button
    $('#resetFilter').on('click', function() {
        currentFilter.category = null;
        currentFilter.year = 2008;
        refreshAllCharts();
        showToast('Filter reset', 'success');
    });
}

// ============================================
// 1. LOAD DASHBOARD STATISTICS (Cards)
// ============================================
function loadDashboardStats() {
    $.ajax({
        url: '../api/get_data.php',
        method: 'GET',
        data: { 
            action: 'dashboard_stats',
            year: currentFilter.year
        },
        dataType: 'json',
        beforeSend: function() {
            // Show loading state
            $('#totalSales').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#totalOrders').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#avgOrder').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#growthRate').html('<i class="fas fa-spinner fa-spin"></i>');
        },
        success: function(response) {
            console.log('Dashboard stats loaded:', response);
            
            // Update cards with animation
            animateValue('totalSales', 0, response.total_sales, 1000, true);
            animateValue('totalOrders', 0, response.total_orders, 1000, false);
            animateValue('avgOrder', 0, response.avg_order, 1000, true);
            
            // Growth rate dengan warna
            const growthClass = response.growth >= 0 ? 'text-success' : 'text-danger';
            const growthIcon = response.growth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            $('#growthRate').html(
                '<span class="' + growthClass + '">' + 
                response.growth.toFixed(1) + '%' +
                ' <i class="fas ' + growthIcon + '"></i></span>'
            );
        },
        error: function(xhr, status, error) {
            console.error('Error loading dashboard stats:', error);
            showToast('Failed to load statistics', 'error');
            
            // Show error state
            $('#totalSales').text('Error');
            $('#totalOrders').text('Error');
            $('#avgOrder').text('Error');
            $('#growthRate').text('Error');
        }
    });
}

// ============================================
// 2. LOAD SALES TREND CHART (Line Chart)
// ============================================
function loadSalesTrendChart() {
    $.ajax({
        url: '../api/get_data.php',
        method: 'GET',
        data: { 
            action: 'sales_trend',
            year: currentFilter.year,
            category: currentFilter.category
        },
        dataType: 'json',
        beforeSend: function() {
            // Show loading indicator on chart
            $('#salesTrendChart').parent().append(
                '<div class="chart-loading"><i class="fas fa-spinner fa-spin fa-3x"></i></div>'
            );
        },
        success: function(data) {
            console.log('Sales trend data loaded:', data);
            
            // Remove loading indicator
            $('.chart-loading').remove();
            
            // Destroy existing chart if exists
            if (salesTrendChart) {
                salesTrendChart.destroy();
            }
            
            const ctx = document.getElementById('salesTrendChart').getContext('2d');
            
            salesTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.month),
                    datasets: [{
                        label: currentFilter.category ? 'Sales: ' + currentFilter.category : 'Total Sales',
                        data: data.map(item => parseFloat(item.total_sales)),
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 12,
                                    family: 'Nunito'
                                },
                                padding: 15
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14,
                                family: 'Nunito'
                            },
                            bodyFont: {
                                size: 13,
                                family: 'Nunito'
                            },
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    return 'Sales: $' + formatNumber(context.parsed.y);
                                },
                                afterLabel: function(context) {
                                    // Tambahan info: persentase dari total
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed.y / total) * 100).toFixed(1);
                                    return 'Share: ' + percentage + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    family: 'Nunito'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                borderDash: [5, 5]
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    family: 'Nunito'
                                },
                                callback: function(value) {
                                    return '$' + formatNumber(value);
                                }
                            }
                        }
                    },
                    onClick: function(event, elements) {
                        // DRILL-DOWN IMPLEMENTATION
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const monthData = data[index];
                            drillDownToMonth(monthData.month, monthData.month_number);
                        }
                    }
                }
            });
            
            // Update chart title if filtered
            if (currentFilter.category) {
                $('.card-header h6').first().text(
                    'Tren penjualan ' + currentFilter.category + ' per bulan'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading sales trend:', error);
            $('.chart-loading').remove();
            showToast('Failed to load sales trend chart', 'error');
        }
    });
}

// ============================================
// 3. LOAD CATEGORY CHART (Doughnut Chart)
// ============================================
function loadCategoryChart() {
    $.ajax({
        url: '../api/get_data.php',
        method: 'GET',
        data: { 
            action: 'sales_by_category',
            year: currentFilter.year
        },
        dataType: 'json',
        beforeSend: function() {
            $('#categoryChart').parent().append(
                '<div class="chart-loading"><i class="fas fa-spinner fa-spin fa-2x"></i></div>'
            );
        },
        success: function(data) {
            console.log('Category data loaded:', data);
            
            $('.chart-loading').remove();
            
            // Destroy existing chart
            if (categoryChart) {
                categoryChart.destroy();
            }
            
            const ctx = document.getElementById('categoryChart').getContext('2d');
            
            // Colors for categories
            const colors = [
                { bg: 'rgba(78, 115, 223, 0.8)', border: 'rgba(78, 115, 223, 1)' },
                { bg: 'rgba(28, 200, 138, 0.8)', border: 'rgba(28, 200, 138, 1)' },
                { bg: 'rgba(54, 185, 204, 0.8)', border: 'rgba(54, 185, 204, 1)' },
                { bg: 'rgba(246, 194, 62, 0.8)', border: 'rgba(246, 194, 62, 1)' },
                { bg: 'rgba(231, 74, 59, 0.8)', border: 'rgba(231, 74, 59, 1)' }
            ];
            
            categoryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(item => item.category_name),
                    datasets: [{
                        data: data.map(item => parseFloat(item.total_sales)),
                        backgroundColor: colors.map(c => c.bg),
                        borderColor: colors.map(c => c.border),
                        borderWidth: 2,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 11,
                                    family: 'Nunito'
                                },
                                padding: 15,
                                boxWidth: 15,
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const value = data.datasets[0].data[i];
                                            const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                            const percentage = ((value / total) * 100).toFixed(1);
                                            
                                            return {
                                                text: label + ' (' + percentage + '%)',
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14,
                                family: 'Nunito'
                            },
                            bodyFont: {
                                size: 13,
                                family: 'Nunito'
                            },
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return [
                                        label,
                                        'Sales: $' + formatNumber(value),
                                        'Share: ' + percentage + '%'
                                    ];
                                }
                            }
                        }
                    },
                    onClick: function(event, elements) {
                        // CROSS-FILTERING IMPLEMENTATION
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const categoryName = data[index].category_name;
                            
                            // Toggle filter
                            if (currentFilter.category === categoryName) {
                                currentFilter.category = null;
                                showToast('Filter removed', 'info');
                            } else {
                                currentFilter.category = categoryName;
                                showToast('Filtered by: ' + categoryName, 'success');
                            }
                            
                            // Refresh charts with new filter
                            loadSalesTrendChart();
                            loadProductTable();
                        }
                    }
                }
            });
            
            // Highlight if category filtered
            if (currentFilter.category) {
                highlightCategoryInChart(currentFilter.category);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading category chart:', error);
            $('.chart-loading').remove();
            showToast('Failed to load category chart', 'error');
        }
    });
}

// ============================================
// 4. LOAD PRODUCT TABLE (DataTable)
// ============================================
function loadProductTable() {
    $.ajax({
        url: '../api/get_data.php',
        method: 'GET',
        data: { 
            action: 'product_sales',
            year: currentFilter.year,
            category: currentFilter.category
        },
        dataType: 'json',
        beforeSend: function() {
            $('#productTableBody').html(
                '<tr><td colspan="5" class="text-center">' +
                '<i class="fas fa-spinner fa-spin"></i> Loading data...</td></tr>'
            );
        },
        success: function(data) {
            console.log('Product table data loaded:', data);
            
            if (data.length === 0) {
                $('#productTableBody').html(
                    '<tr><td colspan="5" class="text-center text-muted">No data available</td></tr>'
                );
                return;
            }
            
            let html = '';
            data.forEach(function(item, index) {
                const growthClass = parseFloat(item.growth) >= 0 ? 'text-success' : 'text-danger';
                const growthIcon = parseFloat(item.growth) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                
                html += '<tr>';
                html += '<td>' + item.product_name + '</td>';
                html += '<td><span class="badge badge-primary">' + item.category_name + '</span></td>';
                html += '<td class="text-right font-weight-bold">$' + formatNumber(item.sales_amount) + '</td>';
                html += '<td class="text-right">' + formatNumber(item.order_qty) + '</td>';
                html += '<td class="text-right ' + growthClass + ' font-weight-bold">';
                html += item.growth + '% <i class="fas ' + growthIcon + ' ml-1"></i>';
                html += '</td>';
                html += '</tr>';
            });
            
            $('#productTableBody').html(html);
            
            // Destroy and reinitialize DataTable
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().destroy();
            }
            
            $('#dataTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "order": [[2, "desc"]], // Sort by sales amount
                "language": {
                    "search": "Search products:",
                    "lengthMenu": "Show _MENU_ products",
                    "info": "Showing _START_ to _END_ of _TOTAL_ products",
                    "infoEmpty": "No products found",
                    "infoFiltered": "(filtered from _MAX_ total products)",
                    "zeroRecords": "No matching products found"
                },
                "columnDefs": [
                    { "orderable": true, "targets": [0, 1, 2, 3, 4] },
                    { "className": "text-center", "targets": [1] }
                ]
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading product table:', error);
            $('#productTableBody').html(
                '<tr><td colspan="5" class="text-center text-danger">' +
                '<i class="fas fa-exclamation-triangle"></i> Error loading data</td></tr>'
            );
            showToast('Failed to load product data', 'error');
        }
    });
}

// ============================================
// DRILL-DOWN FUNCTION
// ============================================
function drillDownToMonth(monthName, monthNumber) {
    console.log('Drilling down to:', monthName);
    
    // Show modal with daily data
    $('#drillDownModal').modal('show');
    $('#drillDownTitle').text('Daily Sales - ' + monthName + ' ' + currentFilter.year);
    
    // Load daily data
    $.ajax({
        url: '../api/get_data.php',
        method: 'GET',
        data: { 
            action: 'daily_sales',
            year: currentFilter.year,
            month: monthNumber,
            category: currentFilter.category
        },
        dataType: 'json',
        beforeSend: function() {
            $('#drillDownContent').html(
                '<div class="text-center p-5">' +
                '<i class="fas fa-spinner fa-spin fa-3x text-primary"></i>' +
                '<p class="mt-3">Loading daily data...</p>' +
                '</div>'
            );
        },
        success: function(data) {
            console.log('Daily data loaded:', data);
            
            // Create bar chart for daily data
            let chartHtml = '<canvas id="dailyChart" height="80"></canvas>';
            chartHtml += '<div class="mt-3"><table class="table table-sm table-hover">';
            chartHtml += '<thead><tr><th>Date</th><th class="text-right">Sales</th><th class="text-right">Orders</th></tr></thead><tbody>';
            
            data.forEach(function(item) {
                chartHtml += '<tr>';
                chartHtml += '<td>' + item.day + '</td>';
                chartHtml += '<td class="text-right">$' + formatNumber(item.sales) + '</td>';
                chartHtml += '<td class="text-right">' + item.orders + '</td>';
                chartHtml += '</tr>';
            });
            
            chartHtml += '</tbody></table></div>';
            $('#drillDownContent').html(chartHtml);
            
            // Render daily chart
            const ctx = document.getElementById('dailyChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.day),
                    datasets: [{
                        label: 'Daily Sales',
                        data: data.map(item => parseFloat(item.sales)),
                        backgroundColor: 'rgba(28, 200, 138, 0.7)',
                        borderColor: 'rgba(28, 200, 138, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
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
        },
        error: function() {
            $('#drillDownContent').html(
                '<div class="alert alert-danger">Failed to load daily data</div>'
            );
        }
    });
}

// ============================================
// HELPER FUNCTIONS
// ============================================

// Format number with thousand separator
function formatNumber(num) {
    if (num === null || num === undefined) return '0';
    return parseFloat(num).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}

// Animate number counting
function animateValue(elementId, start, end, duration, isDollar) {
    const element = document.getElementById(elementId);
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(function() {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        
        const prefix = isDollar ? '$' : '';
        element.textContent = prefix + formatNumber(current);
    }, 16);
}

// Show toast notification
function showToast(message, type = 'info') {
    // Simple toast implementation
    const bgColor = {
        'success': '#1cc88a',
        'error': '#e74a3b',
        'warning': '#f6c23e',
        'info': '#36b9cc'
    };
    
    const toast = $('<div>')
        .addClass('toast-notification')
        .css({
            'position': 'fixed',
            'top': '20px',
            'right': '20px',
            'background': bgColor[type] || bgColor['info'],
            'color': 'white',
            'padding': '15px 20px',
            'border-radius': '5px',
            'box-shadow': '0 4px 6px rgba(0,0,0,0.1)',
            'z-index': 9999,
            'min-width': '250px',
            'animation': 'slideIn 0.3s ease-out'
        })
        .html('<i class="fas fa-info-circle mr-2"></i>' + message);
    
    $('body').append(toast);
    
    setTimeout(function() {
        toast.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

// Highlight selected category in chart
function highlightCategoryInChart(categoryName) {
    // Visual feedback untuk category yang dipilih
    // Implementasi bisa ditambahkan sesuai kebutuhan
}

// Refresh all charts
function refreshAllCharts() {
    loadDashboardStats();
    loadSalesTrendChart();
    loadCategoryChart();
    loadProductTable();
}

// Export data function (bonus feature)
function exportData(format) {
    showToast('Exporting data as ' + format.toUpperCase() + '...', 'info');
    // Implementation for export
}