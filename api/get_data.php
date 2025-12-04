<?php
/**
 * API Endpoint - Adventure Works DWH
 * Handles all AJAX requests from dashboard
 */

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Error reporting (disable di production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Include database connection
require_once '../config/database.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Check connection
if (!$db) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Get action parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';
$year = isset($_GET['year']) ? intval($_GET['year']) : 2008;
$category = isset($_GET['category']) ? $_GET['category'] : null;

try {
    
    switch($action) {
        
        // ==========================================
        // 1. DASHBOARD STATISTICS (Cards)
        // ==========================================
        case 'dashboard_stats':
            $query = "SELECT 
                        COALESCE(SUM(fs.SalesAmount), 0) as total_sales,
                        COUNT(DISTINCT fs.SalesOrderNumber) as total_orders,
                        COALESCE(AVG(fs.SalesAmount), 0) as avg_order
                      FROM fact_sales fs
                      INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
                      WHERE dt.Year = :year";
            
            if ($category) {
                $query .= " AND fs.ProductKey IN (
                    SELECT dp.ProductKey 
                    FROM dim_product dp
                    INNER JOIN dim_category dc ON dp.CategoryKey = dc.CategoryKey
                    WHERE dc.CategoryName = :category
                )";
            }
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            if ($category) {
                $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            }
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Calculate growth (vs previous year)
            $queryPrev = "SELECT 
                            COALESCE(SUM(fs.SalesAmount), 0) as prev_sales
                          FROM fact_sales fs
                          INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
                          WHERE dt.Year = :prev_year";
            
            $stmtPrev = $db->prepare($queryPrev);
            $prevYear = $year - 1;
            $stmtPrev->bindParam(':prev_year', $prevYear, PDO::PARAM_INT);
            $stmtPrev->execute();
            $prevResult = $stmtPrev->fetch(PDO::FETCH_ASSOC);
            
            $growth = 0;
            if ($prevResult['prev_sales'] > 0) {
                $growth = (($result['total_sales'] - $prevResult['prev_sales']) / $prevResult['prev_sales']) * 100;
            }
            
            echo json_encode([
                'total_sales' => floatval($result['total_sales']),
                'total_orders' => intval($result['total_orders']),
                'avg_order' => floatval($result['avg_order']),
                'growth' => round($growth, 2)
            ]);
            break;
            
        // ==========================================
        // 2. SALES TREND (Line Chart)
        // ==========================================
        case 'sales_trend':
            $query = "SELECT 
                        dt.Month as month_number,
                        dt.MonthName as month,
                        COALESCE(SUM(fs.SalesAmount), 0) as total_sales,
                        COUNT(DISTINCT fs.SalesOrderNumber) as order_count
                      FROM dim_time dt
                      LEFT JOIN fact_sales fs ON dt.TimeKey = fs.TimeKey";
            
            if ($category) {
                $query .= " AND fs.ProductKey IN (
                    SELECT dp.ProductKey 
                    FROM dim_product dp
                    INNER JOIN dim_category dc ON dp.CategoryKey = dc.CategoryKey
                    WHERE dc.CategoryName = :category
                )";
            }
            
            $query .= " WHERE dt.Year = :year
                       GROUP BY dt.Month, dt.MonthName
                       ORDER BY dt.Month";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            if ($category) {
                $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            }
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($results);
            break;
            
        // ==========================================
        // 3. SALES BY CATEGORY (Doughnut Chart)
        // ==========================================
        case 'sales_by_category':
            $query = "SELECT 
                        dc.CategoryName as category_name,
                        COALESCE(SUM(fs.SalesAmount), 0) as total_sales,
                        COUNT(DISTINCT fs.SalesOrderNumber) as order_count,
                        COUNT(DISTINCT fs.ProductKey) as product_count
                      FROM fact_sales fs
                      INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
                      INNER JOIN dim_category dc ON dp.CategoryKey = dc.CategoryKey
                      INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
                      WHERE dt.Year = :year
                      GROUP BY dc.CategoryName
                      ORDER BY total_sales DESC";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($results);
            break;
            
        // ==========================================
        // 4. PRODUCT SALES TABLE (DataTable)
        // ==========================================
        case 'product_sales':
            $query = "SELECT 
                        dp.ProductName as product_name,
                        dc.CategoryName as category_name,
                        COALESCE(SUM(fs.SalesAmount), 0) as sales_amount,
                        SUM(fs.OrderQuantity) as order_qty,
                        COUNT(DISTINCT fs.SalesOrderNumber) as order_count
                      FROM fact_sales fs
                      INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
                      INNER JOIN dim_category dc ON dp.CategoryKey = dc.CategoryKey
                      INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
                      WHERE dt.Year = :year";
            
            if ($category) {
                $query .= " AND dc.CategoryName = :category";
            }
            
            $query .= " GROUP BY dp.ProductName, dc.CategoryName
                       ORDER BY sales_amount DESC
                       LIMIT 50";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            if ($category) {
                $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            }
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate growth for each product (vs previous year)
            foreach ($results as &$product) {
                // Query previous year sales
                $queryGrowth = "SELECT COALESCE(SUM(fs.SalesAmount), 0) as prev_sales
                               FROM fact_sales fs
                               INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
                               INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
                               WHERE dt.Year = :prev_year
                               AND dp.ProductName = :product_name";
                
                $stmtGrowth = $db->prepare($queryGrowth);
                $prevYear = $year - 1;
                $stmtGrowth->bindParam(':prev_year', $prevYear, PDO::PARAM_INT);
                $stmtGrowth->bindParam(':product_name', $product['product_name'], PDO::PARAM_STR);
                $stmtGrowth->execute();
                $growthData = $stmtGrowth->fetch(PDO::FETCH_ASSOC);
                
                $growth = 0;
                if ($growthData['prev_sales'] > 0) {
                    $growth = (($product['sales_amount'] - $growthData['prev_sales']) / $growthData['prev_sales']) * 100;
                } elseif ($product['sales_amount'] > 0) {
                    $growth = 100; // New product
                }
                
                $product['growth'] = round($growth, 1);
            }
            
            echo json_encode($results);
            break;
            
        // ==========================================
        // 5. DAILY SALES (Drill-Down)
        // ==========================================
        case 'daily_sales':
            $month = isset($_GET['month']) ? intval($_GET['month']) : 1;
            
            $query = "SELECT 
                        dt.Day as day,
                        dt.FullDate as full_date,
                        COALESCE(SUM(fs.SalesAmount), 0) as sales,
                        COUNT(DISTINCT fs.SalesOrderNumber) as orders,
                        SUM(fs.OrderQuantity) as quantity
                      FROM dim_time dt
                      LEFT JOIN fact_sales fs ON dt.TimeKey = fs.TimeKey";
            
            if ($category) {
                $query .= " AND fs.ProductKey IN (
                    SELECT dp.ProductKey 
                    FROM dim_product dp
                    INNER JOIN dim_category dc ON dp.CategoryKey = dc.CategoryKey
                    WHERE dc.CategoryName = :category
                )";
            }
            
            $query .= " WHERE dt.Year = :year AND dt.Month = :month
                       GROUP BY dt.Day, dt.FullDate
                       ORDER BY dt.Day";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
            if ($category) {
                $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            }
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($results);
            break;
            
        // ==========================================
        // 6. TOP PRODUCTS (For widgets)
        // ==========================================
        case 'top_products':
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
            
            $query = "SELECT 
                        dp.ProductName as product_name,
                        COALESCE(SUM(fs.SalesAmount), 0) as sales_amount
                      FROM fact_sales fs
                      INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
                      INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
                      WHERE dt.Year = :year
                      GROUP BY dp.ProductName
                      ORDER BY sales_amount DESC
                      LIMIT :limit";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($results);
            break;
            
        // ==========================================
        // 7. SALES BY REGION (For geography page)
        // ==========================================
        case 'sales_by_region':
            $query = "SELECT 
                        dg.Country as country,
                        dg.StateProvince as state,
                        dg.City as city,
                        COALESCE(SUM(fs.SalesAmount), 0) as total_sales,
                        COUNT(DISTINCT fs.CustomerKey) as customer_count
                      FROM fact_sales fs
                      INNER JOIN dim_geography dg ON fs.GeographyKey = dg.GeographyKey
                      INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
                      WHERE dt.Year = :year
                      GROUP BY dg.Country, dg.StateProvince, dg.City
                      ORDER BY total_sales DESC";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($results);
            break;
            
        // ==========================================
        // 8. PRODUCT CATEGORY HIERARCHY (Drill-down)
        // ==========================================
        case 'category_hierarchy':
            $categoryParam = isset($_GET['category']) ? $_GET['category'] : null;
            
            if (!$categoryParam) {
                // Level 1: Categories
                $query = "SELECT 
                            dc.CategoryName as name,
                            'category' as level,
                            COALESCE(SUM(fs.SalesAmount), 0) as sales
                          FROM fact_sales fs
                          INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
                          INNER JOIN dim_category dc ON dp.CategoryKey = dc.CategoryKey
                          INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
                          WHERE dt.Year = :year
                          GROUP BY dc.CategoryName
                          ORDER BY sales DESC";
            } else {
                // Level 2: Products in category
                $query = "SELECT 
                            dp.ProductName as name,
                            'product' as level,
                            COALESCE(SUM(fs.SalesAmount), 0) as sales
                          FROM fact_sales fs
                          INNER JOIN dim_product dp ON fs.ProductKey = dp.ProductKey
                          INNER JOIN dim_category dc ON dp.CategoryKey = dc.CategoryKey
                          INNER JOIN dim_time dt ON fs.TimeKey = dt.TimeKey
                          WHERE dt.Year = :year AND dc.CategoryName = :category
                          GROUP BY dp.ProductName
                          ORDER BY sales DESC
                          LIMIT 20";
            }
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            if ($categoryParam) {
                $stmt->bindParam(':category', $categoryParam, PDO::PARAM_STR);
            }
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($results);
            break;
            
        // ==========================================
        // DEFAULT: Invalid action
        // ==========================================
        default:
            echo json_encode([
                'error' => 'Invalid action parameter',
                'available_actions' => [
                    'dashboard_stats',
                    'sales_trend',
                    'sales_by_category',
                    'product_sales',
                    'daily_sales',
                    'top_products',
                    'sales_by_region',
                    'category_hierarchy'
                ]
            ]);
            break;
    }
    
} catch(PDOException $e) {
    // Log error (in production, log to file instead)
    error_log('Database Error: ' . $e->getMessage());
    
    echo json_encode([
        'error' => 'Database query failed',
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
    
} catch(Exception $e) {
    error_log('General Error: ' . $e->getMessage());
    
    echo json_encode([
        'error' => 'An error occurred',
        'message' => $e->getMessage()
    ]);
}

// Close database connection
$db = null;
?>