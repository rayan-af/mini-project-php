<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_role'])) {
    header("Location: index.php");
    exit();
}
$current_page = basename($_SERVER['PHP_SELF']);
$role = ucfirst($_SESSION['user_role']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="bg-dark border-right" id="sidebar-wrapper">
        <div class="sidebar-heading text-white">Cafe Stock</div>
        <div class="list-group list-group-flush">
            <a href="dashboard.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="pos.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'pos.php') ? 'active' : ''; ?>">
                <i class="fas fa-cash-register me-2"></i>POS
            </a>
            <a href="inventory.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'inventory.php') ? 'active' : ''; ?>">
                <i class="fas fa-boxes me-2"></i>Inventory
            </a>
            <a href="recipe_costing.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'recipe_costing.php') ? 'active' : ''; ?>">
                <i class="fas fa-calculator me-2"></i>Recipe Costing
            </a>
            <a href="waste.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'waste.php') ? 'active' : ''; ?>">
                <i class="fas fa-trash-alt me-2"></i>Waste Log
            </a>
            <a href="logout.php" class="list-group-item list-group-item-action text-danger">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <button class="btn btn-primary" id="menu-toggle"><i class="fas fa-bars"></i></button>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <form class="d-flex ms-auto me-3">
                        <input class="form-control me-2" type="search" placeholder="Search..." aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                    <ul class="navbar-nav mt-2 mt-lg-0">
                        <li class="nav-item">
                            <span class="nav-link fw-bold"><i class="fas fa-user-circle me-1"></i> <?php echo $role; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container-fluid p-4">
