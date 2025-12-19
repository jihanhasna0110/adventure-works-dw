<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-database"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Adventure Works <sup>DWH</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Sales Overview</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Analysis
    </div>

    <!-- Nav Item - Product Analysis -->
    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'product_analysis.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="product_analysis.php">
            <i class="fas fa-fw fa-box"></i>
            <span>Product Analysis</span>
        </a>
    </li>

    <!-- Nav Item - Customer Geography -->
    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'customer_geo.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="customer_geo.php">
            <i class="fas fa-fw fa-map-marked-alt"></i>
            <span>Customer Geography</span>
        </a>
    </li>

    <!-- Nav Item - Business Analytics -->
    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'business_analytics.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="business_analytics.php">
            <i class="fas fa-fw fa-chart-pie"></i>
            <span>Business Analytics</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        OLAP
    </div>

    <!-- Nav Item - Mondrian OLAP (Dengan ACTIVE CLASS) -->
    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'olap_mondrian.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="olap_mondrian.php">
            <i class="fas fa-fw fa-cube"></i>
            <span>Mondrian OLAP</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
