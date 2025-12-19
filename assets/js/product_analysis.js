/**
 * Product Analysis JavaScript - LOADING SAMA PERSIS DENGAN DASHBOARD.JS
 * FIXED: Top 5 Products - Perfect balanced sizing & spacing
 */

jQuery(document).ready(function ($) {
  // GLOBAL CHART REFERENCES
  let productTrendChart = null;
  let categoryComparisonChart = null;
  let productMixChart = null;

  const yearSelect = $("#yearFilterProduct");

  console.log("=== PRODUCT ANALYSIS INITIALIZED ===");
  console.log("Initial year value:", yearSelect.val());
  // Inisialisasi pertama
  loadProductAnalysis();

  // Event handler saat year berubah
  yearSelect.on("change", function () {
    const selectedYear = $(this).val();
    console.log("=== YEAR FILTER CHANGED ===");
    console.log("New year:", selectedYear);
    loadProductAnalysis();
  });

  // MASTER LOADER - LOAD ALL COMPONENTS
  function loadProductAnalysis() {
    const year = $("#yearFilterProduct").val();

    console.log("=== LOADING ALL COMPONENTS ===");
    console.log("Year filter:", year);
    console.log("Timestamp:", new Date().toLocaleString());

    // Load semua komponen dengan year parameter yang sama
    loadProductTrendChart(year);
    loadCategoryComparison(year);
    loadProductMix(year);
    loadTopProducts(year);
    loadProductTable(year);
  }

  // =============================
  // 1. PRODUCT TREND CHART (SAMA DENGAN DASHBOARD CHART LOADING)
  // =============================
  function loadProductTrendChart(year) {
    // DASHBOARD PATTERN: tambah chart-loading ke parent canvas
    $("#productTrendChart")
      .parent()
      .append(
        '<div class="chart-loading"><i class="fas fa-spinner fa-spin fa-3x"></i></div>'
      );

    $.ajax({
      url: "../api/get_data.php",
      method: "GET",
      data: { action: "sales_trend", year: year },
      dataType: "json",
      success: function (data) {
        // DASHBOARD PATTERN: hapus chart-loading
        $(".chart-loading").remove();

        if (productTrendChart) {
          productTrendChart.destroy();
        }

        const ctx = document
          .getElementById("productTrendChart")
          .getContext("2d");

        productTrendChart = new Chart(ctx, {
          type: "line",
          data: {
            labels: data.map((d) => d.month),
            datasets: [
              {
                label: "Sales Trend",
                data: data.map((d) => Number(d.total_sales || 0)),
                borderColor: "rgba(231, 74, 59, 1)",
                backgroundColor: "rgba(231, 74, 59, 0.1)",
                borderWidth: 2,
                fill: true,
                tension: 0.4,
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
        $(".chart-loading").remove();
      },
    });
  }

  // =============================
  // 2. CATEGORY COMPARISON (BAR)
  // =============================
  function loadCategoryComparison(year) {
    // DASHBOARD PATTERN
    $("#categoryComparisonChart")
      .parent()
      .append(
        '<div class="chart-loading"><i class="fas fa-spinner fa-spin fa-3x"></i></div>'
      );

    $.ajax({
      url: "../api/get_data.php",
      method: "GET",
      data: { action: "sales_by_category", year: year },
      dataType: "json",
      success: function (data) {
        $(".chart-loading").remove();

        if (categoryComparisonChart) {
          categoryComparisonChart.destroy();
        }

        const ctx = document
          .getElementById("categoryComparisonChart")
          .getContext("2d");

        categoryComparisonChart = new Chart(ctx, {
          type: "bar",
          data: {
            labels: data.map((d) => d.category_name),
            datasets: [
              {
                label: "Sales by Category",
                data: data.map((d) => Number(d.total_sales || 0)),
                backgroundColor: [
                  "rgba(78, 115, 223, 0.8)",
                  "rgba(28, 200, 138, 0.8)",
                  "rgba(54, 185, 204, 0.8)",
                  "rgba(246, 194, 62, 0.8)",
                  "rgba(231, 74, 59, 0.8)",
                ],
                borderWidth: 0,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: "y",
            plugins: {
              legend: { display: false },
              tooltip: {
                callbacks: {
                  label: function (context) {
                    return "Sales: $" + formatNumber(context.parsed.x);
                  },
                },
              },
            },
            scales: {
              x: {
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
        $(".chart-loading").remove();
      },
    });
  }

  // =============================
  // 3. PRODUCT MIX (POLAR AREA)
  // =============================
  function loadProductMix(year) {
    $("#productMixChart")
      .parent()
      .append(
        '<div class="chart-loading"><i class="fas fa-spinner fa-spin fa-3x"></i></div>'
      );

    $.ajax({
      url: "../api/get_data.php",
      method: "GET",
      data: { action: "sales_by_category", year: year },
      dataType: "json",
      success: function (data) {
        $(".chart-loading").remove();

        if (productMixChart) {
          productMixChart.destroy();
        }

        const ctx = document.getElementById("productMixChart").getContext("2d");

        productMixChart = new Chart(ctx, {
          type: "polarArea",
          data: {
            labels: data.map((d) => d.category_name),
            datasets: [
              {
                data: data.map((d) => Number(d.total_sales || 0)),
                backgroundColor: [
                  "rgba(78, 115, 223, 0.6)",
                  "rgba(28, 200, 138, 0.6)",
                  "rgba(54, 185, 204, 0.6)",
                  "rgba(246, 194, 62, 0.6)",
                  "rgba(231, 74, 59, 0.6)",
                ],
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { position: "right" },
            },
          },
        });
      },
      error: function () {
        $(".chart-loading").remove();
      },
    });
  }

  // =============================
  // 4. TOP PRODUCTS LIST (VERSI SIMPLE & BERSIH)
  // =============================
  function loadTopProducts(year) {
    // State loading sederhana
    $("#topProductsList").html(
      '<div class="text-center py-4 text-muted">' +
        '<i class="fas fa-spinner fa-spin mb-2"></i><br>' +
        "<small>Loading top products...</small>" +
        "</div>"
    );

    $.ajax({
      url: "../api/get_data.php",
      method: "GET",
      data: { action: "top_products", year: year, limit: 5 },
      dataType: "json",
      success: function (data) {
        const top5 = data.slice(0, 5);
        let html = '<div class="list-group list-group-flush">';

        top5.forEach(function (item, index) {
          const medal =
            index === 0
              ? "🥇"
              : index === 1
              ? "🥈"
              : index === 2
              ? "🥉"
              : index + 1;

          html +=
            '<div class="list-group-item d-flex justify-content-between align-items-center p-2 mb-2">';

          // Kiri: medal + nama produk (gabungan, satu baris)
          html += '<div class="d-flex align-items-center">';

          // Medal bulat
          html +=
            '<span class="mr-3 d-inline-flex align-items-center justify-content-center" ' +
            'style="width: 60px; height: 40px; border-radius: 50%; ' +
            "background-color: #4e73df; color: #fff; " +
            'font-size: 20px; line-height: 1; padding: 0;">' +
            medal +
            "</span>";

          // Nama produk, gabungan, rapi di kiri
          html +=
            '<strong style="font-size: 13px;">' +
            item.product_name +
            "</strong>";

          html += "</div>"; // tutup kiri

          // Kanan: angka sales
          html +=
            '<strong class="text-success">$' +
            formatNumber(item.sales_amount) +
            "</strong>";

          html += "</div>";
        });

        html += "</div>";
        $("#topProductsList").html(html);
      },
      error: function () {
        $("#topProductsList").html(
          '<div class="text-center text-danger py-4">' +
            '<i class="fas fa-exclamation-triangle mb-2"></i><br>' +
            "<small>Failed to load top products</small>" +
            "</div>"
        );
      },
    });
  }
  // =============================
  // 5. PRODUCT TABLE - FIXED REFRESH & NO CACHE
  // =============================
  function loadProductTable(year) {
    console.log("=== LOADING PRODUCT TABLE ===");
    console.log("Year parameter:", year);
    console.log("Current time:", new Date().toLocaleTimeString());

    // Destroy DataTable first BEFORE changing HTML
    if ($.fn.DataTable.isDataTable("#productAnalysisTable")) {
      $("#productAnalysisTable").DataTable().destroy();
      console.log("DataTable destroyed");
    }

    // Show loading state
    $("#productAnalysisBody").html(
      '<tr><td colspan="7" class="text-center py-4">' +
        '<i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>' +
        "<small>Loading data...</small>" +
        "</td></tr>"
    );

    $.ajax({
      url: "../api/get_data.php",
      method: "GET",
      data: {
        action: "product_sales",
        year: year,
        _: new Date().getTime(), // Cache buster
      },
      dataType: "json",
      cache: false, // Disable cache
      success: function (data) {
        console.log("=== API RESPONSE RECEIVED ===");
        console.log("Year requested:", year);
        console.log("Data length:", data ? data.length : 0);

        if (data && data.length > 0) {
          console.log("First 3 items:");
          console.table(data.slice(0, 3));
        }

        // Check for error
        if (data && data.error) {
          console.error("API Error:", data.error);
          $("#productAnalysisBody").html(
            '<tr><td colspan="7" class="text-center text-danger py-4">' +
              '<i class="fas fa-exclamation-triangle mb-2"></i><br>' +
              "<small>Error: " +
              data.error +
              "</small>" +
              "</td></tr>"
          );
          return;
        }

        // Check if empty
        if (!data || data.length === 0) {
          console.warn("No data returned");
          $("#productAnalysisBody").html(
            '<tr><td colspan="7" class="text-center text-warning py-4">' +
              '<i class="fas fa-info-circle mb-2"></i><br>' +
              "<small>No data available</small>" +
              "</td></tr>"
          );
          return;
        }

        // Build table rows
        let html = "";

        data.forEach(function (item, index) {
          const sales = Number(item.sales_amount || 0);
          const qty = Number(item.order_qty || 0);
          const avgPrice = qty > 0 ? sales / qty : 0;

          // Debug first item
          if (index === 0) {
            console.log("First item detail:", {
              name: item.product_name,
              sales_raw: item.sales_amount,
              sales_parsed: sales,
              qty_raw: item.order_qty,
              qty_parsed: qty,
              growth_raw: item.growth,
            });
          }

          // Handle growth
          const hasGrowth = item.growth !== null && item.growth !== undefined;
          const growthVal = hasGrowth ? Number(item.growth) : null;

          let growthText;
          let statusBadge;

          if (!hasGrowth || growthVal === null || isNaN(growthVal)) {
            growthText = "-";
            statusBadge = '<span class="badge badge-secondary">N/A</span>';
          } else {
            growthText = growthVal.toFixed(1) + "%";

            if (growthVal >= 10) {
              statusBadge =
                '<span class="badge badge-success"><i class="fas fa-arrow-up"></i> Growing</span>';
            } else if (growthVal > 0) {
              statusBadge =
                '<span class="badge badge-info"><i class="fas fa-arrow-up"></i> Slight Growth</span>';
            } else if (growthVal === 0) {
              statusBadge =
                '<span class="badge badge-secondary"><i class="fas fa-minus"></i> Stable</span>';
            } else if (growthVal > -10) {
              statusBadge =
                '<span class="badge badge-warning"><i class="fas fa-arrow-down"></i> Slight Decline</span>';
            } else {
              statusBadge =
                '<span class="badge badge-danger"><i class="fas fa-arrow-down"></i> Declining</span>';
            }
          }

          html += "<tr>";
          html += "<td>" + item.product_name + "</td>";
          html +=
            '<td><span class="badge badge-info">' +
            item.category_name +
            "</span></td>";
          html += '<td class="text-right">$' + formatNumber(sales) + "</td>";
          html += '<td class="text-right">' + formatNumber(qty) + "</td>";
          html += '<td class="text-right">$' + formatNumber(avgPrice) + "</td>";
          html += '<td class="text-right">' + growthText + "</td>";
          html += '<td class="text-center">' + statusBadge + "</td>";
          html += "</tr>";
        });

        // Update table body
        $("#productAnalysisBody").html(html);
        console.log("Table HTML updated");

        // Re-initialize DataTable with fresh data
        setTimeout(function () {
          $("#productAnalysisTable").DataTable({
            order: [[2, "desc"]], // Sort by Sales Amount
            pageLength: 10,
            destroy: true, // Allow re-initialization
            language: {
              emptyTable: "No product data available",
              search: "Search products:",
              lengthMenu: "Show _MENU_ products per page",
            },
          });
          console.log("DataTable re-initialized");
        }, 100);

        console.log("=== TABLE LOADED SUCCESSFULLY ===");
      },
      error: function (xhr, status, error) {
        console.error("=== AJAX ERROR ===");
        console.error("Status:", status);
        console.error("Error:", error);
        console.error("Response Text:", xhr.responseText);

        $("#productAnalysisBody").html(
          '<tr><td colspan="7" class="text-center text-danger py-4">' +
            '<i class="fas fa-exclamation-triangle mb-2"></i><br>' +
            "<small>Error: " +
            error +
            "</small>" +
            "</td></tr>"
        );
      },
    });
  }

  // =============================
  // UTILITIES (SAMA DENGAN DASHBOARD)
  // =============================
  function formatNumber(num) {
    const n = Number(num || 0);
    return n.toLocaleString("en-US", {
      minimumFractionDigits: 0,
      maximumFractionDigits: 2,
    });
  }

  window.exportTableData = function () {
    alert("Exporting data...");
  };
});
