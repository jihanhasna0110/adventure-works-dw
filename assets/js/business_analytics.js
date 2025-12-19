// Business Analytics Dashboard - FULL VERSION
let analyticsCharts = {};

$(document).ready(function () {
  console.log("Business Analytics initializing...");
  loadAnalyticsData();

  $("#yearFilterAnalytics").on("change", loadAnalyticsData);
});

function loadAnalyticsData() {
    const year = $('#yearFilterAnalytics').val() || 'all';
    
    // Stats Cards (tetap)
    loadCardTypeStats(year);
    loadColorStats(year); // ← Cancelled % card tetap ada
    loadAOVStats(year);
    loadSalespersonStats(year);
    
    // Charts - HAPUS status chart
    loadCardTypeChart(year);
    loadColorChart(year);   // ← Ini yang pindah ke kanan
    // loadStatusChart(year);  ← HAPUS INI
    loadAOVChart(year);
    loadSalespersonChart(year);
    
    loadCardTable(year);  // Load table pertama
}


// ============================================
// STATS CARDS
// ============================================
function loadCardTypeStats(year) {
  $.getJSON(
    "../api/get_data.php",
    { action: "top_card_type", year: year },
    function (data) {
      $("#topCardType").text(data.top_type || "--");
      $("#topCardTypeCount").text(
        (data.top_count || 0).toLocaleString() + " tx"
      );
    }
  ).fail(showError);
}

function loadColorStats(year) {
  $.getJSON(
    "../api/get_data.php",
    { action: "top_color", year: year },
    function (data) {
      $("#topColor").text(data.top_color || "--");
      $("#topColorQty").text((data.top_qty || 0).toLocaleString() + " units");
    }
  ).fail(showError);
}


function loadAOVStats(year) {
  $.getJSON(
    "../api/get_data.php",
    { action: "aov_individual", year: year },
    function (data) {
      $("#aovIndividual").text("$" + formatNumber(data.aov || 0));
    }
  ).fail(showError);
}

function loadSalespersonStats(year) {
  $.getJSON(
    "../api/get_data.php",
    { action: "top_salesperson", year: year },
    function (data) {
      $("#topSalesperson").text(data.top_name || "--");
      $("#topSalesAmount").text("$" + formatNumber(data.top_amount || 0));
    }
  ).fail(showError);
}

// ============================================
// CHARTS - FIXED LOADING STATES
// ============================================

function loadCardTypeChart(year) {
  $.getJSON(
    "../api/get_data.php",
    { action: "card_type_dist", year: year },
    function (response) {
      const data = Array.isArray(response) ? response : [];
      if (!data.length) return;

      // ✅ FIX: HAPUS LOADING + SHOW CANVAS
      const $cardBody = $("#cardTypeChart").closest(".card-body");
      $cardBody.find(".text-center").remove();
      $("#cardTypeChart").show();

      destroyChart("cardType");
      const ctx = $("#cardTypeChart")[0].getContext("2d");
      analyticsCharts.cardType = new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: data.map((d) => d.card_type || "Unknown"),
          datasets: [
            {
              data: data.map((d) => d.count || 0),
              backgroundColor: ["#4e73df", "#1cc88a", "#36b9cc", "#f6c23e"],
            },
          ],
        },
        options: { responsive: true, maintainAspectRatio: false },
      });
    }
  ).fail(showError);
}

function loadColorChart(year) {
  $.getJSON(
    "../api/get_data.php",
    { action: "color_dist", year: year },
    function (response) {
      const data = Array.isArray(response) ? response : [];
      if (!data.length) return;

      // ✅ FIX: HAPUS LOADING + SHOW CANVAS
      const $cardBody = $("#colorChart").closest(".card-body");
      $cardBody.find(".text-center").remove();
      $("#colorChart").show();

      destroyChart("color");
      const ctx = $("#colorChart")[0].getContext("2d");
      analyticsCharts.color = new Chart(ctx, {
        type: "pie",
        data: {
          labels: data.map((d) => d.color || "Unknown"),
          datasets: [
            {
              data: data.map((d) => d.quantity || 0),
              backgroundColor: [
                "#ff6384",
                "#36a2eb",
                "#ffce56",
                "#4bc0c0",
                "#9966ff",
              ],
            },
          ],
        },
        options: { responsive: true, maintainAspectRatio: false },
      });
    }
  ).fail(showError);
}

function loadAOVChart(year) {
  $.getJSON(
    "../api/get_data.php",
    { action: "aov_by_type", year: year },
    function (response) {
      const data = Array.isArray(response) ? response : [];
      if (!data.length) return;

      // ✅ FIX: HAPUS LOADING + SHOW CANVAS
      const $cardBody = $("#aovChart").closest(".card-body");
      $cardBody.find(".text-center").remove();
      $("#aovChart").show();

      destroyChart("aov");
      const ctx = $("#aovChart")[0].getContext("2d");
      analyticsCharts.aov = new Chart(ctx, {
        type: "bar",
        data: {
          labels: data.map((d) => d.customer_type || "Unknown"),
          datasets: [
            {
              label: "AOV ($)",
              data: data.map((d) => parseFloat(d.aov || 0)),
              backgroundColor: "#4e73df",
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: { y: { beginAtZero: true } },
        },
      });
    }
  ).fail(showError);
}

function loadSalespersonChart(year) {
  $.getJSON(
    "../api/get_data.php",
    { action: "salesperson_territory", year: year },
    function (response) {
      const data = Array.isArray(response) ? response : [];
      if (!data.length) return;

      // ✅ FIX: HAPUS LOADING + SHOW CANVAS
      const $cardBody = $("#salespersonChart").closest(".card-body");
      $cardBody.find(".text-center").remove();
      $("#salespersonChart").show();

      destroyChart("salesperson");
      const ctx = $("#salespersonChart")[0].getContext("2d");
      analyticsCharts.salesperson = new Chart(ctx, {
        type: "bar",
        data: {
          labels: data.map((d) => `${d.salesperson || "Unknown"} (${d.territory || "N/A"})`),
          datasets: [
            {
              label: "Sales ($)",
              data: data.map((d) => parseFloat(d.amount || 0)),
              backgroundColor: "#1cc88a",
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: { y: { beginAtZero: true } },
        },
      });
    }
  ).fail(showError);
}



function loadAnalyticsTable(year) {
  $.getJSON(
    "../api/get_data.php",
    { action: "analytics_summary", year: year },
    function (response) {
      const data = Array.isArray(response) ? response : [];
      let html = "";
      data.forEach((item) => {
        html += `<tr>
                <td>${item.card_type || "Unknown"}</td>
                <td class="text-right">${(
                  item.count || 0
                ).toLocaleString()}</td>
                <td class="text-right">$${formatNumber(item.amount || 0)}</td>
                <td class="text-right">${(item.pct || 0).toFixed(1)}%</td>
            </tr>`;
      });
      $("#analyticsTableBody").html(
        html ||
          '<tr><td colspan="4" class="text-center text-muted">No data available</td></tr>'
      );
    }
  ).fail(showError);
}

// Tab switching + table loading
$('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
  const target = $(e.target).attr("href");
  const year = $("#yearFilterAnalytics").val();

  if (target === "#cardData") loadCardTable(year);
  if (target === "#colorData") loadColorTable(year);
  if (target === "#statusData") loadStatusTable(year);
});

// Table loading functions
function loadCardTable(year) {
  $.getJSON(
    "../api/get_data.php",
    { action: "card_summary", year: year },
    function (data) {
      let html = "";
      data.forEach((item) => {
        html += `<tr>
                <td>${item.card_type}</td>
                <td class="text-right">${(
                  item.count || 0
                ).toLocaleString()}</td>
                <td class="text-right">$${formatNumber(item.amount)}</td>
                <td class="text-right">${parseFloat(item.pct || 0).toFixed(
                  1
                )}%</td>
            </tr>`;
      });
      $("#cardTableBody").html(html);
    }
  );
}

function loadColorTable(year) {
  $.getJSON(
    "../api/get_data.php",
    { action: "color_dist", year: year },
    function (data) {
      let html = "",
        productCount = 0;
      data.forEach((item) => {
        productCount = Math.floor((item.quantity || 0) / 10); // estimate
        html += `<tr>
                <td><span class="color-dot" style="background: ${getColorForName(
                  item.color
                )}"></span> ${item.color}</td>
                <td class="text-right">${(
                  item.quantity || 0
                ).toLocaleString()}</td>
                <td class="text-right">${productCount}</td>
            </tr>`;
      });
      $("#colorTableBody").html(html);
    }
  );
}

function loadStatusTable(year) {
  $.getJSON(
    "../api/get_data.php",
    { action: "status_summary", year: year },
    function (data) {
      let html = "";
      data.forEach((item) => {
        const statusClass =
          item.status_name === "Cancelled"
            ? "text-danger"
            : item.status_name === "Shipped"
            ? "text-success"
            : "text-info";
        html += `<tr>
                <td><span class="badge badge-info">${
                  item.status_name
                }</span></td>
                <td class="text-right ${statusClass}">${(
          item.count || 0
        ).toLocaleString()}</td>
                <td class="text-right">$${formatNumber(item.amount)}</td>
            </tr>`;
      });
      $("#statusTableBody").html(html);
    }
  );
}

// Sortable table headers
$(document).on("click", ".sortable", function () {
  const $table = $(this).closest("table");
  const colIndex = parseInt($(this).data("col"));
  const $rows = $table.find("tbody tr").get();

  $rows.sort((a, b) => {
    const aVal =
      $(a)
        .find("td")
        .eq(colIndex)
        .text()
        .replace(/[^\d.-]/g, "") || 0;
    const bVal =
      $(b)
        .find("td")
        .eq(colIndex)
        .text()
        .replace(/[^\d.-]/g, "") || 0;
    return parseFloat(aVal) - parseFloat(bVal);
  });

  $table.find("tbody").append($rows);

  // Toggle sort icon
  $(".sortable").removeClass("sorted-asc sorted-desc");
  $(this).toggleClass("sorted-asc sorted-desc");
});

// Color utilities
function getColorForName(colorName) {
  const colors = {
    Black: "#333",
    Silver: "#C0C0C0",
    Red: "#FF0000",
    Blue: "#0000FF",
    Yellow: "#FFFF00",
    Green: "#008000",
  };
  return colors[colorName] || "#999";
}

// Load first table on page load
loadCardTable($("#yearFilterAnalytics").val());

// ============================================
// UTILITIES
// ============================================
function destroyChart(chartKey) {
  if (analyticsCharts[chartKey]) {
    analyticsCharts[chartKey].destroy();
    analyticsCharts[chartKey] = null;
  }
}

function formatNumber(num) {
  return parseFloat(num || 0).toLocaleString("en-US", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  });
}

function showError(xhr, status, error) {
  console.log("API Error:", xhr.responseText);
  // Silent fail - no toast spam
}

function refreshAnalyticsData() {
  loadAnalyticsData();
  showToast("Data refreshed!", "success");
}

function exportAnalyticsData() {
  showToast("Export feature coming soon!", "info");
}

function showToast(message, type = "info") {
  const colors = { success: "#1cc88a", error: "#e74a3b", info: "#36b9cc" };
  const toast = $(`<div class="toast-alert">${message}</div>`).css({
    position: "fixed",
    top: "20px",
    right: "20px",
    background: colors[type],
    color: "white",
    padding: "15px 20px",
    "border-radius": "5px",
    "z-index": 9999,
  });
  $("body").append(toast);
  setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3000);
}
