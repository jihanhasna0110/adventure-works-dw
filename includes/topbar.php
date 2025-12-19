<?php
// Ambil data user
$fullname = $_SESSION['FullName'] ?? 'User';
$role     = $_SESSION['Role'] ?? '';
?>

<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <!-- Kiri: sapaan user + role -->
            <div class="d-none d-sm-flex flex-column" style="padding-left: 30px;">
                <span class="text-gray-800" style="font-size: 1.25rem; font-weight: 700;">
                    Hi, <?php echo htmlspecialchars($fullname); ?>
                </span>
                <span class="text-muted" style="font-size: 1.05rem;">
                    <?php echo htmlspecialchars($role); ?>
                </span>
            </div>

            <!-- Spacer supaya profil tetap di kanan -->
            <div class="ml-auto"></div>

            <!-- Kanan: avatar bulat + tombol titik tiga + dropdown -->
            <ul class="navbar-nav">

                <li class="nav-item d-flex align-items-center mr-2">
                    <!-- Avatar -->
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                         style="width: 50px; height: 50px; font-weight: 600;">
                        <?php echo strtoupper(substr($fullname, 0, 1)); ?>
                    </div>
                </li>

                <!-- Tombol titik tiga (dropdown untuk drilldown / actions) -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="moreActionsDropdown"
                       role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-lg text-gray-500"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                         aria-labelledby="moreActionsDropdown">
                        <h6 class="dropdown-header">Quick Actions</h6>
                        <a class="dropdown-item" href="#" onclick="openGlobalDrilldown()">
                            <i class="fas fa-chart-line fa-sm fa-fw mr-2 text-gray-400"></i>
                            Drilldown Overview
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                            Export Summary
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>

            </ul>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
