/**
 * Customer Geography JavaScript
 * Handles geographic analysis and visualization
 */

// ============================================
// GLOBAL VARIABLES
// ============================================
let geoSalesChart = null;
let countryChart = null;
let stateChart = null;
let currentChartType = "bar";
let geoData = [];

// ============================================
// DOCUMENT READY
// ============================================
$(document).ready(function () {
  console.log("Geography Dashboard initializing...");
  loadGeographyData();

  // Year filter change event
  $("#yearFilterGeo").on("change", function () {
    loadGeographyData();
  });
});

// ============================================
// MAIN DATA LOADER
// ============================================
function loadGeographyData() {
  const year = $("#yearFilterGeo").val();

  // Load all components
  loadGeoStats(year);
  loadGeoChart(year);
  loadCountryChart(year);
  loadStateChart(year);
  loadTopCities(year);
  loadGeoTable(year);
}

// ============================================
// 1. LOAD GEOGRAPHY STATISTICS (Cards)
// ============================================
function loadGeoStats(year) {
  $.ajax({
    url: "../api/get_data.php",
    method: "GET",
    data: {
      action: "sales_by_region",
      year: year,
    },
    dataType: "json",
    beforeSend: function () {
      $("#totalCountries, #totalStates, #totalCities, #totalCustomersGeo").html(
        '<i class="fas fa-spinner fa-spin"></i>'
      );
    },
    success: function (data) {
      geoData = data; // Store for later use

      // Calculate unique counts
      const countries = [...new Set(data.map((item) => item.country))];
      const states = [...new Set(data.map((item) => item.state))];
      const cities = [...new Set(data.map((item) => item.city))];
      const totalCustomers = data.reduce(
        (sum, item) => sum + parseInt(item.customer_count),
        0
      );

      // Animate counts
      animateValue("totalCountries", 0, countries.length, 1000, false);
      animateValue("totalStates", 0, states.length, 1000, false);
      animateValue("totalCities", 0, cities.length, 1000, false);
      animateValue("totalCustomersGeo", 0, totalCustomers, 1000, false);
    },
    error: function (xhr, status, error) {
      console.error("Error loading geo stats:", error);
      $("#totalCountries, #totalStates, #totalCities, #totalCustomersGeo").text(
        "Error"
      );
    },
  });
}

// ============================================
// 2. LOAD MAIN GEOGRAPHIC SALES CHART
// ============================================
function loadGeoChart(year) {
  $.ajax({
    url: "../api/get_data.php",
    method: "GET",
    data: {
      action: "sales_by_region",
      year: year,
    },
    dataType: "json",
    success: function (data) {
      // Group by country
      const countryData = {};
      data.forEach((item) => {
        if (!countryData[item.country]) {
          countryData[item.country] = 0;
        }
        countryData[item.country] += parseFloat(item.total_sales);
      });

      // Destroy existing chart
      if (geoSalesChart) {
        geoSalesChart.destroy();
      }

      const ctx = document.getElementById("geoSalesChart").getContext("2d");

      // Colors for countries
      const colors = [
        "rgba(78, 115, 223, 0.8)",
        "rgba(28, 200, 138, 0.8)",
        "rgba(54, 185, 204, 0.8)",
        "rgba(246, 194, 62, 0.8)",
        "rgba(231, 74, 59, 0.8)",
        "rgba(133, 135, 150, 0.8)",
      ];

      geoSalesChart = new Chart(ctx, {
        type: currentChartType,
        data: {
          labels: Object.keys(countryData),
          datasets: [
            {
              label: "Sales by Country",
              data: Object.values(countryData),
              backgroundColor: colors,
              borderColor: colors.map((c) => c.replace("0.8", "1")),
              borderWidth: 2,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: currentChartType !== "bar",
              position: "right",
              labels: {
                font: {
                  size: 11,
                  family: "Nunito",
                },
                generateLabels: function (chart) {
                  const data = chart.data;
                  if (data.labels.length && data.datasets.length) {
                    return data.labels.map((label, i) => {
                      const value = data.datasets[0].data[i];
                      return {
                        text: label + ": $" + formatNumber(value),
                        fillStyle: data.datasets[0].backgroundColor[i],
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
              callbacks: {
                label: function (context) {
                  const label = context.label || "";
                  const value = context.parsed.y || context.parsed;
                  return label + ": $" + formatNumber(value);
                },
              },
            },
          },
          scales:
            currentChartType === "bar"
              ? {
                  y: {
                    beginAtZero: true,
                    ticks: {
                      callback: function (value) {
                        return "$" + formatNumber(value);
                      },
                    },
                  },
                }
              : {},
          onClick: function (event, elements) {
            if (elements.length > 0) {
              const index = elements[0].index;
              const country = Object.keys(countryData)[index];
              drillDownToCountry(country, year);
            }
          },
        },
      });
    },
    error: function (xhr, status, error) {
      console.error("Error loading geo chart:", error);
    },
  });
}

// ============================================
// 3. LOAD COUNTRY CHART
// ============================================
function loadCountryChart(year) {
  $.ajax({
    url: "../api/get_data.php",
    data: { action: "sales_by_region", year: year },
    dataType: "json",
    success: function (data) {
      // Group by country
      const countryData = {};
      data.forEach((item) => {
        if (!countryData[item.country]) {
          countryData[item.country] = 0;
        }
        countryData[item.country] += parseFloat(item.total_sales);
      });

      if (countryChart) countryChart.destroy();

      const ctx = document.getElementById("countryChart").getContext("2d");
      countryChart = new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: Object.keys(countryData),
          datasets: [
            {
              data: Object.values(countryData),
              backgroundColor: [
                "rgba(78, 115, 223, 0.8)",
                "rgba(28, 200, 138, 0.8)",
                "rgba(54, 185, 204, 0.8)",
                "rgba(246, 194, 62, 0.8)",
              ],
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: "bottom",
            },
          },
        },
      });
    },
  });
}

// ============================================
// 4. LOAD STATE CHART (Top 10)
// ============================================
// ============================================
// 4. LOAD STATE CHART (Top 10) - FINAL
// ============================================
function loadStateChart(year) {
  console.log("🔄 loadStateChart called with year:", year);

  // 1) Hancurkan semua chart yang memakai canvas #stateChart
  if (typeof Chart !== "undefined" && Chart.instances) {
    Object.values(Chart.instances).forEach((ch) => {
      if (ch.canvas && ch.canvas.id === "stateChart") {
        try {
          ch.destroy();
          console.log("🗑️ Destroy old chart id:", ch.id);
        } catch (e) {
          console.warn("Destroy error:", e);
        }
      }
    });
  }

  // 2) Bersihkan reference global
  if (window.stateChart) {
    try {
      window.stateChart.destroy();
    } catch (e) {}
    window.stateChart = null;
  }

  $.ajax({
    url: "../api/get_data.php",
    data: { action: "sales_by_region", year: year },
    dataType: "json",
    success: function (data) {
      console.log("✅ API data length:", data.length);

      // Group by state
      const stateData = {};
      data.forEach((item) => {
        const st = item.state || "Unknown";
        if (!stateData[st]) stateData[st] = 0;
        stateData[st] += parseFloat(item.total_sales || 0);
      });

      // Sort & top 10
      const sortedStates = Object.entries(stateData)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 10);

      console.log("🏆 Top 10 states:", sortedStates);

      const canvas = document.getElementById("stateChart");
      if (!canvas) {
        console.error("❌ Canvas #stateChart tidak ditemukan");
        return;
      }

      const ctx = canvas.getContext("2d");
      // Bersihkan isi canvas
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      // 3) Buat chart baru
      window.stateChart = new Chart(ctx, {
        type: "bar", // BUKAN horizontalBar
        data: {
          labels: sortedStates.map((s) => s[0]),
          datasets: [
            {
              label: "Sales",
              data: sortedStates.map((s) => s[1]),
              backgroundColor: "rgba(28, 200, 138, 0.7)",
              borderColor: "rgba(28, 200, 138, 1)",
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          indexAxis: "y", // bikin horizontal
          plugins: {
            legend: { display: false },
            title: {
              display: true,
              text: "Top 10 States/Provinces by Sales",
            },
          },
          scales: {
            x: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  return "$" + Number(value).toLocaleString();
                },
              },
            },
          },
        },
      });

      console.log("🎉 State chart created successfully");
    },
    error: function (xhr, status, error) {
      console.error("❌ loadStateChart AJAX error:", status, error);
      console.error(xhr.responseText);
    },
  });
}

// ============================================
// 5. LOAD TOP CITIES LIST
// ============================================
function loadTopCities(year) {
  $.ajax({
    url: "../api/get_data.php",
    data: { action: "sales_by_region", year: year },
    dataType: "json",
    beforeSend: function () {
      $("#topCitiesList").html(
        '<div class="text-center text-muted">' +
          '<i class="fas fa-spinner fa-spin"></i> Loading...</div>'
      );
    },
    success: function (data) {
      // Sort by sales
      data.sort(
        (a, b) => parseFloat(b.total_sales) - parseFloat(a.total_sales)
      );

      let html = '<div class="list-group list-group-flush">';

      data.slice(0, 5).forEach((item, index) => {
        const medal =
          index === 0 ? "🥇" : index === 1 ? "🥈" : index === 2 ? "🥉" : "";
        const rankBadge =
          medal ||
          '<span class="badge badge-secondary">' + (index + 1) + "</span>";

        html +=
          '<div class="list-group-item list-group-item-action p-2" style="cursor: pointer;" ';
        html +=
          "onclick=\"showCityDetail('" +
          item.city +
          "', '" +
          item.state +
          "', '" +
          item.country +
          "')\">";
        html +=
          '<div class="d-flex justify-content-between align-items-center">';
        html += "<div>";
        html += '<span class="mr-2">' + rankBadge + "</span>";
        html += "<strong>" + item.city + "</strong><br>";
        html +=
          '<small class="text-muted">' +
          item.state +
          ", " +
          item.country +
          "</small>";
        html += "</div>";
        html += '<div class="text-right">';
        html +=
          '<strong class="text-success">$' +
          formatNumber(item.total_sales) +
          "</strong><br>";
        html +=
          '<small class="text-muted">' +
          item.customer_count +
          " customers</small>";
        html += "</div>";
        html += "</div>";
        html += "</div>";
      });

      html += "</div>";
      $("#topCitiesList").html(html);
    },
    error: function () {
      $("#topCitiesList").html(
        '<div class="alert alert-danger">Failed to load cities</div>'
      );
    },
  });
}

// ============================================
// 6. LOAD GEOGRAPHY TABLE
// ============================================
function loadGeoTable(year) {
  $.ajax({
    url: "../api/get_data.php",
    data: { action: "sales_by_region", year: year },
    dataType: "json",
    beforeSend: function () {
      $("#geoTableBody").html(
        '<tr><td colspan="7" class="text-center">' +
          '<i class="fas fa-spinner fa-spin"></i> Loading data...</td></tr>'
      );
    },
    success: function (data) {
      if (data.length === 0) {
        $("#geoTableBody").html(
          '<tr><td colspan="7" class="text-center text-muted">No data available</td></tr>'
        );
        return;
      }

      // Calculate total sales for market share
      const totalSales = data.reduce(
        (sum, item) => sum + parseFloat(item.total_sales),
        0
      );
      const totalCustomers = data.reduce(
        (sum, item) => sum + parseInt(item.customer_count),
        0
      );

      let html = "";
      data.forEach((item) => {
        const sales = parseFloat(item.total_sales);
        const customers = parseInt(item.customer_count);
        const avgPerCustomer = customers > 0 ? sales / customers : 0;
        const marketShare = totalSales > 0 ? (sales / totalSales) * 100 : 0;

        html +=
          "<tr onclick=\"showCityDetail('" +
          item.city +
          "', '" +
          item.state +
          "', '" +
          item.country +
          '\')" style="cursor: pointer;">';
        html += "<td>" + item.country + "</td>";
        html += "<td>" + item.state + "</td>";
        html += "<td><strong>" + item.city + "</strong></td>";
        html +=
          '<td class="text-right font-weight-bold">$' +
          formatNumber(sales) +
          "</td>";
        html += '<td class="text-right">' + customers + "</td>";
        html +=
          '<td class="text-right">$' + formatNumber(avgPerCustomer) + "</td>";
        html += '<td class="text-right">';
        html += '<div class="progress" style="height: 20px;">';
        html +=
          '<div class="progress-bar bg-info" role="progressbar" style="width: ' +
          marketShare +
          '%">';
        html += marketShare.toFixed(1) + "%";
        html += "</div></div></td>";
        html += "</tr>";
      });

      $("#geoTableBody").html(html);

      // Update footer
      $("#footerTotalSales").text("$" + formatNumber(totalSales));
      $("#footerTotalCustomers").text(totalCustomers);

      // Initialize/Reinitialize DataTable
      if ($.fn.DataTable.isDataTable("#geoTable")) {
        $("#geoTable").DataTable().destroy();
      }

      $("#geoTable").DataTable({
        pageLength: 10,
        order: [[3, "desc"]], // Sort by sales
        language: {
          search: "Search locations:",
          lengthMenu: "Show _MENU_ locations",
          info: "Showing _START_ to _END_ of _TOTAL_ locations",
        },
      });
    },
    error: function (xhr, status, error) {
      console.error("Error loading geo table:", error);
      $("#geoTableBody").html(
        '<tr><td colspan="7" class="text-center text-danger">' +
          '<i class="fas fa-exclamation-triangle"></i> Error loading data</td></tr>'
      );
    },
  });
}

// ============================================
// DRILL-DOWN FUNCTIONS
// ============================================
function drillDownToCountry(country, year) {
  console.log("Drilling down to country:", country);

  // Filter data by country
  const countryData = geoData.filter((item) => item.country === country);

  // Show in modal or update chart
  alert(
    "Drill-down to " +
      country +
      "\n" +
      "Total cities: " +
      countryData.length +
      "\n" +
      "Implement detailed view here"
  );
}

function showCityDetail(city, state, country) {
  $("#cityDetailModal").modal("show");
  $("#cityDetailModalLabel").text("Sales Details: " + city + ", " + state);

  $("#cityDetailContent").html(
    '<div class="text-center p-5">' +
      '<i class="fas fa-spinner fa-spin fa-3x text-primary"></i>' +
      '<p class="mt-3">Loading city details...</p>' +
      "</div>"
  );

  // Load city detail data
  const year = $("#yearFilterGeo").val();

  setTimeout(function () {
    let detailHtml = '<div class="row">';
    detailHtml += '<div class="col-md-6">';
    detailHtml += "<h5>Location Information</h5>";
    detailHtml += '<table class="table table-sm">';
    detailHtml += "<tr><th>City:</th><td>" + city + "</td></tr>";
    detailHtml += "<tr><th>State/Province:</th><td>" + state + "</td></tr>";
    detailHtml += "<tr><th>Country:</th><td>" + country + "</td></tr>";
    detailHtml += "</table>";
    detailHtml += "</div>";
    detailHtml += '<div class="col-md-6">';
    detailHtml += "<h5>Performance Metrics</h5>";
    detailHtml +=
      '<p class="text-muted">Detailed metrics will be loaded here...</p>';
    detailHtml += "</div>";
    detailHtml += "</div>";

    $("#cityDetailContent").html(detailHtml);
  }, 500);
}

// ============================================
// CHART TYPE CHANGER
// ============================================
function changeGeoChartType(type) {
  currentChartType = type;
  const year = $("#yearFilterGeo").val();
  loadGeoChart(year);
}

// ============================================
// UTILITY FUNCTIONS
// ============================================
function formatNumber(num) {
  if (num === null || num === undefined) return "0";
  return parseFloat(num).toLocaleString("en-US", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  });
}

function animateValue(elementId, start, end, duration, isDollar) {
  const element = document.getElementById(elementId);
  const range = end - start;
  const increment = range / (duration / 16);
  let current = start;

  const timer = setInterval(function () {
    current += increment;
    if (
      (increment > 0 && current >= end) ||
      (increment < 0 && current <= end)
    ) {
      current = end;
      clearInterval(timer);
    }

    const prefix = isDollar ? "$" : "";
    element.textContent = prefix + Math.round(current).toLocaleString();
  }, 16);
}

function refreshGeoData() {
  loadGeographyData();
  showToast("Data refreshed successfully!", "success");
}

function exportGeoData() {
  alert("Exporting geography data to Excel...\nFeature coming soon!");
}
function showToast(message, type) {
  const bgColors = {
    success: "#1cc88a",
    error: "#e74a3b",
    info: "#36b9cc",
  };
  const toast = $("<div>")
    .css({
      position: "fixed",
      top: "20px",
      right: "20px",
      background: bgColors[type] || bgColors["info"],
      color: "white",
      padding: "15px 20px",
      "border-radius": "5px",
      "box-shadow": "0 4px 6px rgba(0,0,0,0.1)",
      "z-index": 9999,
    })
    .text(message);

  $("body").append(toast);
  setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3000);
}
