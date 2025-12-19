/**
 * Dashboard JavaScript - Adventure Works DWH
 * Handles data loading, chart rendering, and interactivity
 */

// ============================================
// GLOBAL VARIABLES
// ============================================
let salesTrendChart = null;
let categoryChart = null;

// Inisialisasi filter hanya sekali di global
let currentFilter = {
  category: null,
  year: "all",
};

// ============================================
// DOCUMENT READY
// ============================================
$(document).ready(function () {
  initializeDashboard();
});

// ============================================
// INITIALIZATION
// ============================================
function initializeDashboard() {
  setupEventListeners();
  refreshAllCharts();
}

// ============================================
// EVENT LISTENERS
// ============================================
function setupEventListeners() {
  // Set default dropdown
  $("#yearFilter").val("all");

  // Ganti tahun -> hanya ubah year, JANGAN sentuh category
  $("#yearFilter").on("change", function () {
    currentFilter.year = $(this).val();
    refreshAllCharts();
  });

  // Reset filter -> satu‑satunya tempat yang boleh mengosongkan category
  $("#resetFilter").on("click", function () {
    currentFilter.category = null;
    currentFilter.year = "all";
    $("#yearFilter").val("all");
    refreshAllCharts();
    showToast("Filter reset to All Years", "success");
  });
}

// ============================================
// HELPER: FORMAT & ANIMASI ANGKA
// ============================================
function formatNumber(num) {
  if (num === null || num === undefined || isNaN(num)) return "0";
  return Number(num).toLocaleString("en-US", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  });
}

function animateValueSafe(elementId, start, end, duration, isDollar) {
  const element = document.getElementById(elementId);
  if (!element) return;

  start = Number(start) || 0;
  end = Number(end) || 0;

  const range = end - start;
  const steps = Math.max(1, Math.floor(duration / 16));
  const increment = range / steps;
  let current = start;
  let step = 0;

  const timer = setInterval(function () {
    step++;
    current += increment;

    if (step >= steps) {
      current = end;
      clearInterval(timer);
    }

    const prefix = isDollar ? "$" : "";
    element.textContent = prefix + formatNumber(current);
  }, 16);
}

// ============================================
// 1. LOAD DASHBOARD STATISTICS (Cards)
// ============================================
function loadDashboardStats() {
  $.ajax({
    url: "../api/get_data.php",
    method: "GET",
    data: {
      action: "dashboard_stats",
      year: currentFilter.year,
      category: currentFilter.category,
    },
    dataType: "json",
    beforeSend: function () {
      $("#totalSales, #totalOrders, #avgOrder, #growthRate").html(
        '<i class="fas fa-spinner fa-spin"></i>'
      );
    },
    success: function (response) {
      console.log("Dashboard stats loaded:", response);

      const totalSales = Number(response.total_sales || 0);
      const totalOrders = Number(response.total_orders || 0);
      const avgOrder = Number(response.avg_order || 0);
      const growth = Number(response.growth || 0);

      animateValueSafe("totalSales", 0, totalSales, 1200, true);
      animateValueSafe("totalOrders", 0, totalOrders, 1200, false);
      animateValueSafe("avgOrder", 0, avgOrder, 1200, true);

      const growthClass = growth >= 0 ? "text-success" : "text-danger";
      const growthIcon = growth >= 0 ? "fa-arrow-up" : "fa-arrow-down";
      $("#growthRate").html(
        `<span class="${growthClass}">${growth.toFixed(
          1
        )}% <i class="fas ${growthIcon}"></i></span>`
      );
    },
    error: function () {
      $("#totalSales, #totalOrders, #avgOrder, #growthRate").text("Error");
    },
  });
}

// ============================================
// 2. LOAD SALES TREND CHART (Line Chart)
// ============================================
function loadSalesTrendChart() {
  $.ajax({
    url: "../api/get_data.php",
    method: "GET",
    data: {
      action: "sales_trend",
      year: currentFilter.year,
      category: currentFilter.category,
    },
    dataType: "json",
    beforeSend: function () {
      $("#salesTrendChart")
        .parent()
        .append(
          '<div class="chart-loading"><i class="fas fa-spinner fa-spin fa-3x"></i></div>'
        );
    },
    success: function (data) {
      console.log("Sales trend data loaded:", data);
      $(".chart-loading").remove();

      if (salesTrendChart) {
        salesTrendChart.destroy();
      }

      const ctx = document.getElementById("salesTrendChart").getContext("2d");
      const values = data.map((item) => Number(item.total_sales || 0));

      salesTrendChart = new Chart(ctx, {
        type: "line",
        data: {
          labels: data.map((item) => item.month),
          datasets: [
            {
              label: currentFilter.category
                ? "Sales: " + currentFilter.category
                : "Total Sales",
              data: values,
              backgroundColor: "rgba(78, 115, 223, 0.05)",
              borderColor: "rgba(78, 115, 223, 1)",
              borderWidth: 2,
              pointRadius: 4,
              pointBackgroundColor: "rgba(78, 115, 223, 1)",
              pointBorderColor: "#fff",
              pointBorderWidth: 2,
              pointHoverRadius: 6,
              pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
              pointHoverBorderColor: "#fff",
              pointHoverBorderWidth: 2,
              fill: true,
              tension: 0.3,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: {
            mode: "index",
            intersect: false,
          },
          plugins: {
            legend: {
              display: true,
              position: "top",
              labels: {
                font: { size: 12, family: "Nunito" },
                padding: 15,
              },
            },
            tooltip: {
              enabled: true,
              backgroundColor: "rgba(0, 0, 0, 0.8)",
              titleFont: { size: 14, family: "Nunito" },
              bodyFont: { size: 13, family: "Nunito" },
              padding: 12,
              displayColors: true,
              callbacks: {
                label: function (context) {
                  return "Sales: $" + formatNumber(context.parsed.y);
                },
                afterLabel: function (context) {
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage =
                    total > 0
                      ? ((context.parsed.y / total) * 100).toFixed(1)
                      : "0.0";
                  return "Share: " + percentage + "%";
                },
              },
            },
          },
          scales: {
            x: {
              grid: { display: false },
              ticks: { font: { size: 11, family: "Nunito" } },
            },
            y: {
              beginAtZero: true,
              grid: {
                color: "rgba(0, 0, 0, 0.05)",
                borderDash: [5, 5],
              },
              ticks: {
                font: { size: 11, family: "Nunito" },
                callback: function (value) {
                  return "$" + formatNumber(value);
                },
              },
            },
          },
          onClick: function (event, elements) {
            if (elements.length > 0) {
              const index = elements[0].index;
              const monthData = data[index];
              drillDownToMonth(monthData.month, monthData.month_number);
            }
          },
        },
      });

      if (currentFilter.category) {
        $(".card-header h6")
          .first()
          .text("Tren penjualan " + currentFilter.category + " per bulan");
      }
    },
    error: function () {
      $(".chart-loading").remove();
      showToast("Failed to load sales trend chart", "error");
    },
  });
}

// ============================================
// 3. LOAD CATEGORY CHART (Doughnut Chart)
// ============================================
function loadCategoryChart() {
  $.ajax({
    url: "../api/get_data.php",
    method: "GET",
    data: {
      action: "sales_by_category",
      year: currentFilter.year,
    },
    dataType: "json",
    beforeSend: function () {
      $("#categoryChart")
        .parent()
        .append(
          '<div class="chart-loading"><i class="fas fa-spinner fa-spin fa-2x"></i></div>'
        );
    },
    success: function (data) {
      console.log("Category data loaded:", data);
      $(".chart-loading").remove();

      if (categoryChart) {
        categoryChart.destroy();
      }

      const ctx = document.getElementById("categoryChart").getContext("2d");

      const colors = [
        { bg: "rgba(78, 115, 223, 0.8)", border: "rgba(78, 115, 223, 1)" },
        { bg: "rgba(28, 200, 138, 0.8)", border: "rgba(28, 200, 138, 1)" },
        { bg: "rgba(54, 185, 204, 0.8)", border: "rgba(54, 185, 204, 1)" },
        { bg: "rgba(246, 194, 62, 0.8)", border: "rgba(246, 194, 62, 1)" },
        { bg: "rgba(231, 74, 59, 0.8)", border: "rgba(231, 74, 59, 1)" },
      ];

      const values = data.map((item) => Number(item.total_sales || 0));

      categoryChart = new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: data.map((item) => item.category_name),
          datasets: [
            {
              data: values,
              backgroundColor: colors.map((c) => c.bg),
              borderColor: colors.map((c) => c.border),
              borderWidth: 2,
              hoverOffset: 15,
            },
          ],
        },
        options: {
          // ✅ UKURAN SELALU SAMA
          responsive: true,
          maintainAspectRatio: false,
          aspectRatio: 1.4, // Lebar:tinggi = 1.4:1 (konsisten)

          // ✅ INNER RADIUS BIAR SELALU "DONUT" (tidak berubah jadi pie)
          circumference: 360,
          rotation: -90,
          cutout: "60%", // ✅ Ketebalan donut selalu sama

          plugins: {
            legend: {
              position: "bottom",
              labels: {
                font: { size: 11, family: "Nunito" },
                padding: 15,
                boxWidth: 15,
                generateLabels: function (chart) {
                  const d = chart.data;
                  if (d.labels.length && d.datasets.length) {
                    const total = d.datasets[0].data.reduce((a, b) => a + b, 0);
                    return d.labels.map((label, i) => {
                      const value = d.datasets[0].data[i];
                      const percentage =
                        total > 0 ? ((value / total) * 100).toFixed(1) : "0.0";
                      return {
                        text: label + " (" + percentage + "%)",
                        fillStyle: d.datasets[0].backgroundColor[i],
                        hidden: false,
                        index: i,
                      };
                    });
                  }
                  return [];
                },
              },
            },
            tooltip: {
              enabled: true,
              backgroundColor: "rgba(0, 0, 0, 0.8)",
              titleFont: { size: 14, family: "Nunito" },
              bodyFont: { size: 13, family: "Nunito" },
              padding: 12,
              callbacks: {
                label: function (context) {
                  const label = context.label || "";
                  const value = context.parsed;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage =
                    total > 0 ? ((value / total) * 100).toFixed(1) : "0.0";
                  return [
                    label,
                    "Sales: $" + formatNumber(value),
                    "Share: " + percentage + "%",
                  ];
                },
              },
            },
          },
          onClick: function (event, elements) {
            if (elements.length > 0) {
              const index = elements[0].index;
              const categoryName = data[index].category_name;

              if (currentFilter.category === categoryName) {
                currentFilter.category = null;
                showToast("Filter removed", "info");
              } else {
                currentFilter.category = categoryName;
                showToast("Filtered by: " + categoryName, "success");
              }

              refreshAllCharts();
            }
          },
        },
      });

      if (currentFilter.category) {
        highlightCategoryInChart(currentFilter.category);
      }
    },
    error: function () {
      $(".chart-loading").remove();
      showToast("Failed to load category chart", "error");
    },
  });
}

// ============================================
// 4. LOAD PRODUCT TABLE (DataTable)
// ============================================
function loadProductTable() {
  $.ajax({
    url: "../api/get_data.php",
    method: "GET",
    data: {
      action: "product_sales",
      year: currentFilter.year,
      category: currentFilter.category,
    },
    dataType: "json",
    beforeSend: function () {
      $("#productTableBody").html(
        '<tr><td colspan="5" class="text-center">' +
          '<i class="fas fa-spinner fa-spin"></i> Loading data...</td></tr>'
      );
    },
    success: function (data) {
      console.log(
        "Product table data loaded for",
        currentFilter.category || "ALL",
        "year:",
        currentFilter.year,
        "- rows:",
        Array.isArray(data) ? data.length : 0
      );

      // 1. Hancurkan DataTable lama kalau ada
      if ($.fn.DataTable.isDataTable("#dataTable")) {
        $("#dataTable").DataTable().clear().destroy();
      }

      // 2. Kalau tidak ada data
      if (!Array.isArray(data) || data.length === 0) {
        $("#productTableBody").html(
          '<tr><td colspan="5" class="text-center text-muted">No data available</td></tr>'
        );
        return;
      }

      // 3. Bangun HTML body baru
      const isAllYears = currentFilter.year === "all";
      let html = "";

      data.forEach(function (item) {
        const growthVal = Number(item.growth || 0);

        // LOGIKA OPSI 3: tampilan growth beda saat All Years
        let growthHtml = "";
        if (isAllYears) {
          // All years → growth dianggap tidak relevan, tampil label khusus
          growthHtml =
            '<span class="text-muted font-italic">N/A (All Years)</span>';
        } else {
          // Tahun spesifik → pakai warna hijau/merah seperti biasa
          const growthClass = growthVal >= 0 ? "text-success" : "text-danger";
          const growthIcon = growthVal >= 0 ? "fa-arrow-up" : "fa-arrow-down";

          growthHtml =
            '<span class="' +
            growthClass +
            ' font-weight-bold">' +
            formatNumber(growthVal) +
            '% <i class="fas ' +
            growthIcon +
            ' ml-1"></i></span>';
        }

        html += "<tr>";
        html += "<td>" + item.product_name + "</td>";
        html +=
          '<td><span class="badge badge-primary">' +
          item.category_name +
          "</span></td>";
        html +=
          '<td class="text-right font-weight-bold">$' +
          formatNumber(item.sales_amount) +
          "</td>";
        html +=
          '<td class="text-right">' + formatNumber(item.order_qty) + "</td>";
        html += '<td class="text-right">' + growthHtml + "</td>";
        html += "</tr>";
      });

      // 4. Masukkan HTML ke tbody
      $("#productTableBody").html(html);

      // 5. Inisialisasi ulang DataTable
      $("#dataTable").DataTable({
        pageLength: 10,
        lengthMenu: [
          [10, 25, 50, -1],
          [10, 25, 50, "All"],
        ],
        order: [[2, "desc"]],
        destroy: true,
        language: {
          search: "Search products:",
          lengthMenu: "Show _MENU_ products",
          info: "Showing _START_ to _END_ of _TOTAL_ products",
          infoEmpty: "No products found",
          infoFiltered: "(filtered from _MAX_ total products)",
          zeroRecords: "No matching products found",
        },
        columnDefs: [
          { orderable: true, targets: [0, 1, 2, 3, 4] },
          { className: "text-center", targets: [1] },
          { className: "text-right", targets: [2, 3, 4] },
        ],
      });
    },
    error: function () {
      $("#productTableBody").html(
        '<tr><td colspan="5" class="text-center text-danger">' +
          '<i class="fas fa-exclamation-triangle"></i> Error loading data</td></tr>'
      );
      showToast("Failed to load product data", "error");
    },
  });
}

// ============================================
// DRILL-DOWN FUNCTION
// ============================================
function drillDownToMonth(monthName, monthNumber) {
  console.log("Drilling down to:", monthName);

  $("#drillDownModal").modal("show");
  $("#drillDownTitle").text(
    "Daily Sales - " + monthName + " " + currentFilter.year
  );

  $.ajax({
    url: "../api/get_data.php",
    method: "GET",
    data: {
      action: "daily_sales",
      year: currentFilter.year,
      month: monthNumber,
      category: currentFilter.category,
    },
    dataType: "json",
    beforeSend: function () {
      $("#drillDownContent").html(
        '<div class="text-center p-5">' +
          '<i class="fas fa-spinner fa-spin fa-3x text-primary"></i>' +
          '<p class="mt-3">Loading daily data...</p>' +
          "</div>"
      );
    },
    success: function (data) {
      console.log("Daily data loaded:", data);

      // ================================
      // HTML: CHART + TABLE DENGAN SCROLL
      // ================================
      let chartHtml = `
        <div style="position: relative; height: 220px; width: 100%;">
          <canvas id="dailyChart"></canvas>
        </div>
        <div class="mt-3" style="max-height: 260px; overflow-y: auto;">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th>Date</th>
                <th class="text-right">Sales</th>
                <th class="text-right">Orders</th>
              </tr>
            </thead>
            <tbody>
      `;

      data.forEach(function (item) {
        chartHtml += `
          <tr>
            <td>${item.day}</td>
            <td class="text-right">$${formatNumber(item.sales)}</td>
            <td class="text-right">${item.orders}</td>
          </tr>
        `;
      });

      chartHtml += `
            </tbody>
          </table>
        </div>
      `;

      $("#drillDownContent").html(chartHtml);

      // ================================
      // CHART.JS: BAR CHART HARIAN
      // ================================
      const ctx = document.getElementById("dailyChart").getContext("2d");
      new Chart(ctx, {
        type: "bar",
        data: {
          labels: data.map((item) => item.day),
          datasets: [
            {
              label: "Daily Sales",
              data: data.map((item) => Number(item.sales || 0)),
              backgroundColor: "rgba(28, 200, 138, 0.7)",
              borderColor: "rgba(28, 200, 138, 1)",
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return "Sales: $" + formatNumber(context.parsed.y);
                },
              },
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  return "$" + formatNumber(value);
                },
              },
            },
          },
        },
      });
    },
    error: function () {
      $("#drillDownContent").html(
        '<div class="alert alert-danger">Failed to load daily data</div>'
      );
    },
  });
}

// ============================================
// UTILITIES
// ============================================
function showToast(message, type = "info") {
  const bgColor = {
    success: "#1cc88a",
    error: "#e74a3b",
    warning: "#f6c23e",
    info: "#36b9cc",
  };

  const toast = $("<div>")
    .addClass("toast-notification")
    .css({
      position: "fixed",
      top: "20px",
      right: "20px",
      background: bgColor[type] || bgColor["info"],
      color: "white",
      padding: "15px 20px",
      "border-radius": "5px",
      "box-shadow": "0 4px 6px rgba(0,0,0,0.1)",
      "z-index": 9999,
      "min-width": "250px",
      animation: "slideIn 0.3s ease-out",
    })
    .html('<i class="fas fa-info-circle mr-2"></i>' + message);

  $("body").append(toast);

  setTimeout(function () {
    toast.fadeOut(300, function () {
      $(this).remove();
    });
  }, 3000);
}

function highlightCategoryInChart(categoryName) {
  // Bisa diisi efek highlight kalau diperlukan
}

// ============================================
// REFRESH SEMUA KOMPONEN
// ============================================
function refreshAllCharts() {
  loadDashboardStats();
  loadSalesTrendChart();
  loadCategoryChart();
  loadProductTable();
}

// ============================================
// EXPORT
// ============================================
function exportData(format) {
  showToast("Exporting data as " + format.toUpperCase() + "...", "info");
}
