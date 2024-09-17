<aside class="main-sidebar sidebar-light-primary elevation-2" style="background: #F6F6F6;">
    <!-- Brand Logo -->
    <a href="" class="brand-link" style="background: #275DAD;">
        <img src="../../dist/img/logo.png" alt="Logo" class="brand-image" style="opacity: .8;">
        <span class="brand-text" style="font-size:17px;color:#fff">E-Record System</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="../../dist/img/user1.png" class="img-circle" alt="User Image">
            </div>
            <div class="info">
                <a class="d-block" style="font-size:16px; color:black"><?= htmlspecialchars($_SESSION['fname']); ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <?php if ($_SERVER['REQUEST_URI'] == "/erecord_v2/page/admin_reviewer/record_status.php") { ?>
                        <a href="record_status.php" class="nav-link active active-nav" >
                        <?php } else { ?>
                            <a href="record_status.php" class="nav-link">
                            <?php } ?>
                            <img src="../../dist/img/check-list.png" class="icon-image" style="height:25px;">
                            <p class="pl-1 active-p" style="font-size:16px;">
                                Record Status
                            </p>
                            </a>
                      
                </li>
                <li class="nav-item">
                    <?php if ($_SERVER['REQUEST_URI'] == "/erecord_v2/page/admin_reviewer/list_of_req.php") { ?>
                        <a href="list_of_req.php" class="nav-link active active-nav">
                        <?php } else { ?>
                            <a href="list_of_req.php" class="nav-link">
                            <?php } ?>
                            <img src="../../dist/img/list.png" class="icon-image" style="height:25px;">
                            <p class="pl-1 active-p" style="font-size:16px; color:black">
                                List of Request
                            </p>
                            </a>
                </li>
                <li class="nav-item">
                    <?php if ($_SERVER['REQUEST_URI'] == "/erecord_v2/page/admin_reviewer/dashboard.php") { ?>
                        <a href="dashboard.php" class="nav-link active active-nav">
                        <?php } else { ?>
                            <a href="dashboard.php" class="nav-link">
                            <?php } ?>
                            <img src="../../dist/img/file.png" class="icon-image" style="height:25px;">
                            <p class="pl-1 active-p" style="font-size:16px; color:black">
                                Cancellation Request
                            </p>
                            </a>
                </li>
                <li class="nav-item">
                    <?php if ($_SERVER['REQUEST_URI'] == "/erecord_v2/page/admin_reviewer/viewpage.php") { ?>
                        <a href="viewpage.php" class="nav-link active active-nav">
                        <?php } else { ?>
                            <a href="viewpage.php" class="nav-link">
                            <?php } ?>
                            <img src="../../dist/img/view.png" class="icon-image" style="height:25px;">
                            <p class="pl-1 active-p" style="font-size:16px; color:black">
                                View Data
                            </p>
                            </a>
                </li>
                <li class="nav-item">
                    <?php if ($_SERVER['REQUEST_URI'] == "/erecord_v2/page/admin_reviewer/import_exportpage.php") { ?>
                        <a href="import_exportpage.php" class="nav-link active active-nav">
                        <?php } else { ?>
                            <a href="import_exportpage.php" class="nav-link">
                            <?php } ?>
                            <img src="../../dist/img/add-user.png" class="icon-image" style="height:25px;">
                            <p class="pl-1 active-p" style="font-size:16px; color:black">
                                Add Records
                            </p>
                            </a>
                </li>
                <li class="nav-item">
                    <?php if ($_SERVER['REQUEST_URI'] == "/erecord_v2/page/admin_reviewer/updatepage.php") { ?>
                        <a href="updatepage.php" class="nav-link active active-nav">
                        <?php } else { ?>
                            <a href="updatepage.php" class="nav-link">
                            <?php } ?>
                            <img src="../../dist/img/backup.png" class="icon-image" style="height:25px;">
                            <p class="pl-1 active-p" style="font-size:16px; color:black">
                                Update Data
                            </p>
                            </a>
                </li>
                <li class="nav-item">
                    <?php if ($_SERVER['REQUEST_URI'] == "/erecord_v2/page/admin_reviewer/manpowerpage.php") { ?>
                        <a href="manpowerpage.php" class="nav-link active active-nav">
                        <?php } else { ?>
                            <a href="manpowerpage.php" class="nav-link">
                            <?php } ?>
                            <img src="../../dist/img/group.png" class="icon-image" style="height:25px;">
                            <p class="pl-1 active-p" style="font-size:16px; color:black">
                                Masterlist
                            </p>
                            </a>
                </li>
                <li class="nav-item">
                    <?php if ($_SERVER['REQUEST_URI'] == "/erecord_v2/page/admin_reviewer/accountpage.php") { ?>
                        <a href="accountpage.php" class="nav-link active active-nav">
                        <?php } else { ?>
                            <a href="accountpage.php" class="nav-link">
                            <?php } ?>
                            <img src="../../dist/img/account.png" class="icon-image" style="height:25px;">
                            <p class="pl-1 active-p" style="font-size:16px; color:black">
                                Account Management
                            </p>
                            </a>
                </li>
                <?php include 'logout.php'; ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <div class="sidebar-bottom">
    <p class="text-muted text-center" style="font-size: 14px; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);">Version 2.0.0</p>
  </div>
    <!-- /.sidebar -->
</aside>