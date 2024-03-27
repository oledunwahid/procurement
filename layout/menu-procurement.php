<?php $page =  $_GET['page']; ?>
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="../index.php?page=Dashboard" class="logo logo-dark">
            <span class="logo-sm">
                <img src="assets/images/logo_MAA.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo_MAAA.png" alt="" height="35">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="../index.php?page=Dashboard" class="logo logo-light">
            <span class="logo-sm">
                <img src="assets/images/logo_MAA.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo_MAAA.png" alt="" height="35">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>

            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Procurement Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link<?php if ($page == 'Dashboard') echo 'active'; ?>" href="index.php?page=Dashboard" aria-expanded="false">
                        <i class="ri-dashboard-2-line"></i> <span> Dashboard </span>
                    </a>
                </li>

                <!-- Procurement Menu -->
                <li class="nav-item">
                    <a class="nav-link menu-link <?php if ($page == 'PurchaseRequests') echo 'active'; ?>" href="index.php?page=PurchaseRequests" aria-expanded="false">
                        <i class="ri-shopping-bag-2-line"></i> <span>Purchase Requests</span>
                    </a>
                </li>

                <!-- Administrator Access (if applicable) -->
                <?php if (isset($row7['admin']) && ($row7['admin'] == '1')) : ?>
                    <li class="menu-title"><span data-key="t-menu">Admin Access</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link<?= ($page == 'Administrator') ? ' active' : ''; ?>" href="#admin" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="admin">
                            <i class="ri-admin-fill"></i> <span>Administrator</span>
                        </a>
                        <div class="collapse menu-dropdown" id="admin">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="index.php?page=editFAQ" class="nav-link">Edit FAQ</a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=MenuAdministrator" class="nav-link"> Menu Admin </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

            </ul>


        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>