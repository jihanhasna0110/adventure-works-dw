<?php

/**
 * API Endpoint - Adventure Works DWH
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$action   = isset($_GET['action'])   ? $_GET['action']   : '';
$yearRaw  = isset($_GET['year'])     ? $_GET['year']     : '2004';
$category = isset($_GET['category']) ? $_GET['category'] : null;

// biarkan string, supaya bisa "all" atau "2004"
$year = $yearRaw;

// ===============================
// 0. AVAILABLE YEARS
// ===============================
if ($action === 'available_years') {
    $query = "SELECT DISTINCT dt.Year as year
              FROM dim_time dt
              INNER JOIN fact_sales fs ON dt.TimeKey = fs.TimeKey
              ORDER BY dt.Year DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($years);
    exit();
}

try {

    switch ($action) {

        // ===============================
        // 1. DASHBOARD STATISTICS (KPI)
        // ===============================
        case 'dashboard_stats':
            $year     = $_GET['year']     ?? '2004';
            $category = $_GET['category'] ?? null;

            $conditions = [];
            $params     = [];

            if ($year !== 'all') {
                $conditions[] = 'dt.Year = ?';
                $params[]     = (int)$year;
            }

            if (!empty($category)) {
                $conditions[] = 'dp.CategoryName = ?';  // sesuaikan nama kolom
                $params[]     = $category;
            }

            $whereSql = '';
            if (!empty($conditions)) {
                $whereSql = 'WHERE ' . implode(' AND ', $conditions);
            }

            $sqlMain = "
        SELECT 
            COALESCE(SUM(fs.LineTotal), 0) AS total_sales,
            COUNT(fs.SalesKey)             AS total_orders,
            COALESCE(AVG(fs.LineTotal), 0) AS avg_order
        FROM fact_sales fs
        INNER JOIN dim_time   dt ON fs.TimeKey = dt.TimeKey
        INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
        $whereSql
    ";

            $stmtMain = $db->prepare($sqlMain);
            $stmtMain->execute($params);
            $rowMain = $stmtMain->fetch(PDO::FETCH_ASSOC);

            // Growth (sederhana, tanpa kategori dulu)
            $growth = 0.0;
            if ($year !== 'all') {
                // total sekarang (sudah difilter year + category)
                $totalSales = (float)$rowMain['total_sales'];

                // siapkan kondisi prev year
                $prevConditions = ['dt.Year = ?'];
                $prevParams     = [(int)$year - 1];

                if (!empty($category)) {
                    $prevConditions[] = 'dp.CategoryName = ?';   // sesuaikan nama kolom
                    $prevParams[]     = $category;
                }

                $prevWhere = 'WHERE ' . implode(' AND ', $prevConditions);

                $sqlPrev = "
        SELECT COALESCE(SUM(fs.LineTotal), 0) AS prev_sales
        FROM fact_sales fs
        INNER JOIN dim_time   dt ON fs.TimeKey   = dt.TimeKey
        INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
        $prevWhere
    ";

                $stmtPrev = $db->prepare($sqlPrev);
                $stmtPrev->execute($prevParams);
                $rowPrev  = $stmtPrev->fetch(PDO::FETCH_ASSOC);

                $prevSales = (float)$rowPrev['prev_sales'];

                $growth = 0.0;
                if ($prevSales > 0) {
                    $growth = (($totalSales - $prevSales) / $prevSales) * 100.0;
                }
            }
            if ($year === 'all') {
                $rangeConditions = [];
                $rangeParams     = [];

                if (!empty($category)) {
                    $rangeConditions[] = 'dp.CategoryName = ?';
                    $rangeParams[]     = $category;
                }

                $rangeWhere = '';
                if (!empty($rangeConditions)) {
                    $rangeWhere = 'WHERE ' . implode(' AND ', $rangeConditions);
                }

                $sqlRange = "
        SELECT MIN(dt.Year) AS min_year,
               MAX(dt.Year) AS max_year
        FROM fact_sales fs
        INNER JOIN dim_time   dt ON fs.TimeKey   = dt.TimeKey
        INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
        $rangeWhere
    ";

                $stmtRange = $db->prepare($sqlRange);
                $stmtRange->execute($rangeParams);
                $rowRange  = $stmtRange->fetch(PDO::FETCH_ASSOC);

                $minYear = !empty($rowRange['min_year']) ? (int)$rowRange['min_year'] : null;
                $maxYear = !empty($rowRange['max_year']) ? (int)$rowRange['max_year'] : null;

                $growth = 0.0;

                if ($minYear && $maxYear && $minYear !== $maxYear) {
                    // sales tahun pertama
                    $firstConds  = ['dt.Year = ?'];
                    $firstParams = [$minYear];

                    if (!empty($category)) {
                        $firstConds[]  = 'dp.CategoryName = ?';
                        $firstParams[] = $category;
                    }

                    $firstWhere = 'WHERE ' . implode(' AND ', $firstConds);

                    $sqlFirst = "
            SELECT COALESCE(SUM(fs.LineTotal), 0) AS first_sales
            FROM fact_sales fs
            INNER JOIN dim_time   dt ON fs.TimeKey   = dt.TimeKey
            INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
            $firstWhere
        ";

                    $stmtFirst = $db->prepare($sqlFirst);
                    $stmtFirst->execute($firstParams);
                    $rowFirst  = $stmtFirst->fetch(PDO::FETCH_ASSOC);

                    $firstSales = (float)$rowFirst['first_sales'];

                    // sales tahun terakhir
                    $lastConds  = ['dt.Year = ?'];
                    $lastParams = [$maxYear];

                    if (!empty($category)) {
                        $lastConds[]  = 'dp.CategoryName = ?';
                        $lastParams[] = $category;
                    }

                    $lastWhere = 'WHERE ' . implode(' AND ', $lastConds);

                    $sqlLast = "
            SELECT COALESCE(SUM(fs.LineTotal), 0) AS last_sales
            FROM fact_sales fs
            INNER JOIN dim_time   dt ON fs.TimeKey   = dt.TimeKey
            INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
            $lastWhere
        ";

                    $stmtLast = $db->prepare($sqlLast);
                    $stmtLast->execute($lastParams);
                    $rowLast  = $stmtLast->fetch(PDO::FETCH_ASSOC);

                    $lastSales = (float)$rowLast['last_sales'];

                    if ($firstSales > 0) {
                        $growth = (($lastSales - $firstSales) / $firstSales) * 100.0;
                    }
                }
            }

            echo json_encode([
                'total_sales'  => (float)$rowMain['total_sales'],
                'total_orders' => (int)$rowMain['total_orders'],
                'avg_order'    => (float)$rowMain['avg_order'],
                'growth'       => round($growth, 2)
            ]);
            break;


        // ===============================
        // 2. SALES TREND (Line Chart)
        // ===============================
        case 'sales_trend':
            $conditions = [];
            $params     = [];

            if ($year !== 'all') {
                $conditions[]     = 'dt.Year = :year';
                $params[':year']  = (int)$year;
            }

            if ($category) {
                $conditions[]         = 'dp.CategoryName = :category';
                $params[':category']  = $category;
            }

            $whereSql = '';
            if (!empty($conditions)) {
                $whereSql = 'WHERE ' . implode(' AND ', $conditions);
            }

            $query = "SELECT 
                        dt.Month       AS month_number,
                        dt.MonthName   AS month,
                        COALESCE(SUM(fs.LineTotal), 0)          AS total_sales,
                        COUNT(DISTINCT fs.SalesOrderID)         AS order_count
                      FROM dim_time dt
                      LEFT JOIN fact_sales fs ON dt.TimeKey = fs.TimeKey
                      LEFT JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
                      $whereSql
                      GROUP BY dt.Month, dt.MonthName
                      ORDER BY dt.Month";

            $stmt = $db->prepare($query);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($results);
            break;

        // ===============================
        // 3. SALES BY CATEGORY (Doughnut)
        // ===============================
        case 'sales_by_category':
            $where  = '';
            $params = [];

            // Selalu tampilkan semua kategori, tapi filter tahun kalau spesifik
            if ($year !== 'all') {
                $where           = 'WHERE dt.Year = :year';
                $params[':year'] = (int)$year;
            }

            $query = "SELECT 
                dp.CategoryName                          AS category_name,
                COALESCE(SUM(fs.LineTotal), 0)          AS total_sales,
                COUNT(DISTINCT fs.SalesOrderID)         AS order_count,
                COUNT(DISTINCT fs.ProductKey)           AS product_count
              FROM dim_product dp  -- LEFT JOIN dari product
              LEFT JOIN fact_sales fs ON dp.ProductKey = fs.ProductKey
              LEFT JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
              $where
              GROUP BY dp.CategoryName
              ORDER BY total_sales DESC";

            $stmt = $db->prepare($query);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, PDO::PARAM_INT);
            }
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'sales_by_region':
            $year = isset($_GET['year']) ? $_GET['year'] : 'all';

            $conditions = [];
            $params     = [];

            if ($year !== 'all' && is_numeric($year)) {
                $conditions[]    = 'dt.Year = :year';
                $params[':year'] = (int)$year;
            }

            $whereSql = '';
            if (!empty($conditions)) {
                $whereSql = 'WHERE ' . implode(' AND ', $conditions);
            }

            $sql = "
        SELECT
            t.CountryRegionCode      AS country,
            t.TerritoryName          AS state,
            t.TerritoryName          AS city,
            COALESCE(SUM(fs.LineTotal), 0)      AS total_sales,
            COUNT(DISTINCT fs.CustomerKey)      AS customer_count
        FROM fact_sales fs
        INNER JOIN dim_time      dt ON fs.TimeKey      = dt.TimeKey
        INNER JOIN dim_territory t  ON fs.TerritoryKey = t.TerritoryKey
        $whereSql
        GROUP BY t.CountryRegionCode, t.TerritoryName
        ORDER BY total_sales DESC
    ";

            $stmt = $db->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v, PDO::PARAM_INT);
            }
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_NUMERIC_CHECK);
            break;




        // ===============================
        // 4. PRODUCT SALES TABLE - FINAL WORKING VERSION
        // ===============================
        case 'product_sales':
            $limit      = 50;
            $conditions = [];
            $params     = [];

            // Ambil filter dari request
            $year     = $_GET['year']     ?? 'all';
            $category = $_GET['category'] ?? null;

            // Filter tahun (boleh "all")
            if ($year !== 'all' && is_numeric($year)) {
                $conditions[]      = 'dt.Year = :year';
                $params[':year']   = (int)$year;
            }

            // Filter kategori
            if (!empty($category)) {
                $conditions[]         = 'dp.CategoryName = :category';
                $params[':category']  = $category;
            }

            $where = '';
            if (!empty($conditions)) {
                $where = 'WHERE ' . implode(' AND ', $conditions);
            }

            // Query utama: total sales per product
            $query = "SELECT 
        dp.ProductName                          AS product_name,
        dp.CategoryName                         AS category_name,
        COALESCE(SUM(fs.LineTotal), 0)         AS sales_amount,
        SUM(fs.OrderQuantity)                  AS order_qty,
        COUNT(DISTINCT fs.SalesOrderID)        AS order_count
      FROM fact_sales fs
      INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
      INNER JOIN dim_time dt   ON fs.TimeKey   = dt.TimeKey
      $where
      GROUP BY dp.ProductName, dp.CategoryName
      ORDER BY sales_amount DESC
      LIMIT $limit";

            $stmt = $db->prepare($query);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ==============================
            // GROWTH CALCULATION
            // ==============================
            if ($year !== 'all' && is_numeric($year)) {
                // Specific year selected: calculate growth vs previous year
                $currentYear = (int)$year;
                $prevYear    = $currentYear - 1;

                foreach ($results as &$product) {
                    $currentSales = (float)$product['sales_amount'];

                    // Build conditions for previous year
                    $prevConds  = ['dt2.Year = :prev_year', 'dp2.ProductName = :product_name'];
                    $prevParams = [
                        ':prev_year'    => $prevYear,
                        ':product_name' => $product['product_name'],
                    ];

                    if (!empty($category)) {
                        $prevConds[]             = 'dp2.CategoryName = :category';
                        $prevParams[':category'] = $category;
                    }

                    $prevWhere = 'WHERE ' . implode(' AND ', $prevConds);

                    // Query previous year sales
                    $gQuery = "SELECT COALESCE(SUM(fs2.LineTotal), 0) AS prev_sales
               FROM fact_sales fs2
               INNER JOIN dim_product dp2 ON fs2.ProductKey = dp2.ProductKey
               INNER JOIN dim_time dt2   ON fs2.TimeKey   = dt2.TimeKey
               $prevWhere";

                    $gStmt = $db->prepare($gQuery);
                    foreach ($prevParams as $pKey => $pVal) {
                        $gStmt->bindValue($pKey, $pVal, is_int($pVal) ? PDO::PARAM_INT : PDO::PARAM_STR);
                    }
                    $gStmt->execute();
                    $gRow = $gStmt->fetch(PDO::FETCH_ASSOC);

                    $prevSales = (float)($gRow['prev_sales'] ?? 0);

                    // Calculate growth percentage
                    $growth = 0.0;
                    if ($prevSales > 0) {
                        $growth = (($currentSales - $prevSales) / $prevSales) * 100.0;
                    } elseif ($currentSales > 0) {
                        // New product in current year
                        $growth = 100.0;
                    }

                    $product['growth'] = round($growth, 1);
                }
                unset($product);
            } else {
                // "All Years" selected: no growth calculation
                foreach ($results as &$product) {
                    $product['growth'] = null;
                }
                unset($product);
            }

            // Return JSON with numeric values
            header('Content-Type: application/json');
            echo json_encode($results, JSON_NUMERIC_CHECK);
            exit;

            // ===============================
            // 5. DAILY SALES (DRILL-DOWN)
            // ===============================
        case 'daily_sales':
            $month = isset($_GET['month']) ? (int)$_GET['month'] : 1;

            // di sini wajar kalau hanya support tahun tertentu
            $yearInt = ($year === 'all') ? 2004 : (int)$year;

            $conditions = ['dt.Year = :year', 'dt.Month = :month'];
            $params     = [
                ':year'  => $yearInt,
                ':month' => $month
            ];

            if ($category) {
                $conditions[]        = 'dp.CategoryName = :category';
                $params[':category'] = $category;
            }

            $where = 'WHERE ' . implode(' AND ', $conditions);

            $query = "SELECT 
                        dt.DayOfMonth                      AS day,
                        dt.FullDate                        AS full_date,
                        COALESCE(SUM(fs.LineTotal), 0)    AS sales,
                        COUNT(DISTINCT fs.SalesOrderID)   AS orders,
                        SUM(fs.OrderQuantity)             AS quantity
                      FROM dim_time dt
                      LEFT JOIN fact_sales fs ON dt.TimeKey = fs.TimeKey
                      LEFT JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
                      $where
                      GROUP BY dt.DayOfMonth, dt.FullDate
                      ORDER BY dt.DayOfMonth";

            $stmt = $db->prepare($query);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        // ===============================
        // 6. TOP PRODUCTS
        // ===============================
        case 'top_products':
            // Ambil parameter year & limit dari request
            $year  = isset($_GET['year']) ? $_GET['year'] : 'all';
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

            if ($year === 'all') {
                // Semua tahun - TANPA filter tahun
                $query = "
            SELECT 
                dp.ProductName                     AS product_name,
                COALESCE(SUM(fs.LineTotal), 0)     AS sales_amount
            FROM fact_sales fs
            INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
            GROUP BY dp.ProductName
            ORDER BY sales_amount DESC
            LIMIT :limit
        ";

                $stmt = $db->prepare($query);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            } else {
                // Tahun spesifik
                $query = "
            SELECT 
                dp.ProductName                     AS product_name,
                COALESCE(SUM(fs.LineTotal), 0)     AS sales_amount
            FROM fact_sales fs
            INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
            INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
            WHERE dt.Year = :year
            GROUP BY dp.ProductName
            ORDER BY sales_amount DESC
            LIMIT :limit
        ";

                $stmt = $db->prepare($query);
                $stmt->bindValue(':year', (int)$year, PDO::PARAM_INT);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            }

            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;



        // ===============================
        // BUSINESS ANALYTICS
        // ===============================

        // ===============================
        case 'top_card_type':
            $year = $_GET['year'] ?? 'all';

            $sql = "
        SELECT 
            cc.CardType,
            COUNT(*) AS count,
            SUM(fs.LineTotal) AS total_amount
        FROM fact_sales fs
        INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
        INNER JOIN dim_creditcard cc ON fs.CreditCardKey = cc.CreditCardKey
        WHERE 1=1
    ";
            $params = [];

            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }

            $sql .= " GROUP BY cc.CardType ORDER BY count DESC LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'top_type' => $row['CardType'] ?? 'Cash/Other',
                'top_count' => (int)($row['count'] ?? 0)
            ]);
            break;

        case 'card_type_dist':
            $year = $_GET['year'] ?? 'all';

            $sql = "
        SELECT 
            COALESCE(cc.CardType, 'Cash/Other') AS card_type,
            COUNT(*) AS count,
            SUM(fs.LineTotal) AS total_amount
        FROM fact_sales fs
        INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
        INNER JOIN dim_creditcard cc ON fs.CreditCardKey = cc.CreditCardKey
        WHERE 1=1
    ";
            $params = [];

            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }

            $sql .= " GROUP BY cc.CardType ORDER BY count DESC LIMIT 10";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        // ===============================
        case 'top_color':
            $year = $_GET['year'] ?? 'all';
            $sql = "
        SELECT 
            COALESCE(dp.Color, 'Unknown') AS color,
            SUM(fs.OrderQuantity) AS qty
        FROM fact_sales fs
        INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
        INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
        WHERE 1=1
    ";
            $params = [];
            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }
            $sql .= " GROUP BY dp.Color ORDER BY qty DESC LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'top_color' => $row['color'] ?? 'Unknown',
                'top_qty' => (int)($row['qty'] ?? 0)
            ]);
            break;

        case 'order_status_stats':
            $year = $_GET['year'] ?? 'all';
            $sql = "
        SELECT 
            SUM(CASE WHEN fs.OrderStatus = 1 THEN 1 ELSE 0 END) as shipped_count,
            SUM(CASE WHEN fs.OrderStatus = 5 THEN 1 ELSE 0 END) as cancelled_count,
            COUNT(*) as total_orders
        FROM fact_sales fs
        INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
        WHERE 1=1
    ";
            $params = [];
            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $pct = $row['total_orders'] > 0 ? round(($row['cancelled_count'] / $row['total_orders']) * 100, 1) : 0;

            echo json_encode([
                'cancelled_count' => (int)($row['cancelled_count'] ?? 0),
                'cancelled_pct' => $pct
            ]);
            break;

        case 'aov_individual':
            $year = $_GET['year'] ?? 'all';
            $sql = "
        SELECT AVG(fs.LineTotal) as aov
        FROM fact_sales fs
        INNER JOIN dim_customer dc ON fs.CustomerKey = dc.CustomerKey
        INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
        WHERE dc.CustomerType = 'Individual'
    ";
            $params = [];
            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode(['aov' => (float)($row['aov'] ?? 0)]);
            break;

        case 'top_salesperson':
            $year = $_GET['year'] ?? 'all';
            $sql = "
        SELECT 
            dsp.FullName as salesperson_name,
            COALESCE(dt2.TerritoryName, 'Unknown') AS territory,
            SUM(fs.LineTotal) as amount
        FROM fact_sales fs
        INNER JOIN dim_salesperson dsp ON fs.SalespersonKey = dsp.SalespersonKey
        INNER JOIN dim_territory dt2 ON fs.TerritoryKey = dt2.TerritoryKey
        INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
        WHERE 1=1
    ";
            $params = [];
            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }
            $sql .= " GROUP BY dsp.FullName, dt2.TerritoryName ORDER BY amount DESC LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'top_name' => $row['salesperson_name'] ?? '--',
                'top_amount' => (float)($row['amount'] ?? 0)
            ]);
            break;



        case 'color_dist':
            $year = $_GET['year'] ?? 'all';
            $sql = "
        SELECT 
            COALESCE(dp.Color, 'Unknown') AS color,
            SUM(fs.OrderQuantity) AS quantity
        FROM fact_sales fs
        INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
        INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
        WHERE 1=1
    ";
            $params = [];
            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }
            $sql .= " GROUP BY dp.Color ORDER BY quantity DESC LIMIT 8";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'status_dist':
            $year = $_GET['year'] ?? 'all';
            $sql = "
        SELECT 
            fs.OrderStatus AS status_code,
            CASE fs.OrderStatus
                WHEN 1 THEN 'Shipped'
                ELSE CONCAT('Status ', fs.OrderStatus)
            END AS status_name,
            COUNT(*) AS count,
            SUM(fs.LineTotal) AS total_amount
        FROM fact_sales fs
        INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
        WHERE 1=1
    ";
            $params = [];
            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }
            $sql .= " GROUP BY fs.OrderStatus ORDER BY count DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;


        case 'aov_by_type':
            $year = $_GET['year'] ?? 'all';
            $sql = "
                SELECT 
                    COALESCE(dc.CustomerType, 'Unknown') AS customer_type,
                    AVG(fs.LineTotal) AS aov,
                    COUNT(DISTINCT fs.SalesOrderID) AS order_count,
                    SUM(fs.LineTotal) AS total_revenue
                FROM fact_sales fs
                INNER JOIN dim_customer dc ON fs.CustomerKey = dc.CustomerKey
                INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
                WHERE 1=1
            ";
            $params = [];
            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }
            $sql .= " GROUP BY dc.CustomerType";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'salesperson_territory':
            $year = $_GET['year'] ?? 'all';
            $sql = "
                SELECT 
                    dsp.FullName AS salesperson,
                    COALESCE(dt2.TerritoryName, 'Unknown') AS territory,
                    SUM(fs.LineTotal) AS amount,
                    COUNT(*) AS order_count
                FROM fact_sales fs
                INNER JOIN dim_salesperson dsp ON fs.SalespersonKey = dsp.SalespersonKey
                INNER JOIN dim_territory dt2 ON fs.TerritoryKey = dt2.TerritoryKey
                INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
                WHERE 1=1
            ";
            $params = [];
            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }
            $sql .= " GROUP BY dsp.FullName, dt2.TerritoryName ORDER BY amount DESC LIMIT 12";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'analytics_summary':
            $year = $_GET['year'] ?? 'all';

            // Total count dulu
            $totalSql = "
        SELECT COUNT(*) as total_count 
        FROM fact_sales fs 
        INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
        WHERE 1=1";
            $totalParams = [];
            if ($year !== 'all' && is_numeric($year)) {
                $totalSql .= " AND dt.Year = ?";
                $totalParams[] = (int)$year;
            }

            $totalStmt = $db->prepare($totalSql);
            $totalStmt->execute($totalParams);
            $totalCount = (int)$totalStmt->fetchColumn();

            // Main query
            $sql = "
        SELECT 
            cc.CardType AS card_type,
            COUNT(*) AS count,
            SUM(fs.LineTotal) AS amount
        FROM fact_sales fs
        INNER JOIN dim_creditcard cc ON fs.CreditCardKey = cc.CreditCardKey
        INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
        WHERE 1=1";
            $params = [];
            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }
            $sql .= " GROUP BY cc.CardType ORDER BY count DESC LIMIT 10";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Hitung % di PHP
            foreach ($results as &$row) {
                $row['pct'] = $totalCount > 0 ? round(($row['count'] / $totalCount) * 100, 1) : 0;
            }

            echo json_encode($results);
            break;

        case 'card_summary':
            $year = $_GET['year'] ?? 'all';
            $totalSql = "SELECT COUNT(*) as total FROM fact_sales fs INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey WHERE 1=1";
            $params = [];
            if ($year !== 'all' && is_numeric($year)) {
                $totalSql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }
            $totalStmt = $db->prepare($totalSql);
            $totalStmt->execute($params);
            $totalCount = (int)$totalStmt->fetchColumn();

            $sql = "
        SELECT 
            cc.CardType AS card_type,
            COUNT(*) AS count,
            SUM(fs.LineTotal) AS amount
        FROM fact_sales fs
        INNER JOIN dim_creditcard cc ON fs.CreditCardKey = cc.CreditCardKey
        INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
        WHERE 1=1";
            $params = [];
            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }
            $sql .= " GROUP BY cc.CardType ORDER BY count DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as &$row) {
                $row['pct'] = $totalCount > 0 ? round(($row['count'] / $totalCount) * 100, 1) : 0;
            }
            echo json_encode($results);
            break;

        case 'status_summary':
            $year = $_GET['year'] ?? 'all';
            $sql = "
        SELECT 
            CASE fs.OrderStatus
                WHEN 1 THEN 'Shipped'
                ELSE CONCAT('Status ', fs.OrderStatus)
            END AS status_name,
            COUNT(*) AS count,
            SUM(fs.LineTotal) AS amount
        FROM fact_sales fs
        INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
        WHERE 1=1";
            $params = [];
            if ($year !== 'all' && is_numeric($year)) {
                $sql .= " AND dt.Year = ?";
                $params[] = (int)$year;
            }
            $sql .= " GROUP BY fs.OrderStatus ORDER BY count DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;
    }
} catch (PDOException $e) {
    error_log('DB Error: ' . $e->getMessage());
    echo json_encode([
        'error'   => 'Database query failed',
        'message' => $e->getMessage(),
        'code'    => $e->getCode()
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Server error']);
}

$db = null;
