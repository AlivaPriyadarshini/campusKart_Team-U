<?php

require_once "../config/database.php";


/* ==========================================
   CHECK ADMIN LOGIN
   ========================================== */

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}


/* ==========================================
   COUNT PRODUCTS
   ========================================== */

$productResult = $conn->query(
    "SELECT COUNT(*) AS total FROM products"
);

if (!$productResult) {
    die("Unable to count products: " . $conn->error);
}

$productCount = $productResult->fetch_assoc()['total'];


/* ==========================================
   COUNT ORDERS
   ========================================== */

$orderResult = $conn->query(
    "SELECT COUNT(*) AS total FROM orders"
);

if (!$orderResult) {
    die("Unable to count orders: " . $conn->error);
}

$orderCount = $orderResult->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - CampusKart</title>

    <link
        rel="stylesheet"
        href="../css/dashboard.css">

</head>


<body>


    <!-- ==============================
     ADMIN NAVBAR
     ============================== -->

    <header class="admin-nav">

        <div class="admin-logo">
            <img src="../image/WhatsApp Image 2026-08-29 at 12.40.25 PM.jpeg"
                alt="CampusKart Logo">
        </div>


        <nav>

            <a href="dashboard.php" class="active">
                Dashboard
            </a>

            <a href="product.php">
                Products
            </a>

            <a href="add_product.php">
                Add Product
            </a>

            <a href="../index.php">
                Website
            </a>

            <a href="login.php?logout=1" class="logout">
                Logout
            </a>

        </nav>

    </header>



    <!-- ==============================
     DASHBOARD
     ============================== -->

    <section class="dashboard">


        <!-- Dashboard Heading -->

        <div class="dashboard-heading">

            <div>

                <p class="dashboard-label">
                    ADMINISTRATION
                </p>

                <h1>
                    Dashboard
                </h1>

                <p>
                    Welcome to the CampusKart management panel.
                    Manage your products and orders from here.
                </p>

            </div>


            <a
                href="add_product.php"
                class="dashboard-btn">
                + Add Product
            </a>

        </div>



        <!-- ==============================
         STATISTICS
         ============================== -->

        <div class="stats">


            <!-- Products -->

            <div class="stat-card">

                <div class="stat-icon">
                    🛍️
                </div>

                <div>

                    <p>
                        TOTAL PRODUCTS
                    </p>

                    <h2>
                        <?= htmlspecialchars($productCount) ?>
                    </h2>

                    <span>
                        Products available
                    </span>

                </div>

            </div>



            <!-- Orders -->

            <div class="stat-card">

                <div class="stat-icon">
                    📦
                </div>

                <div>

                    <p>
                        TOTAL ORDERS
                    </p>

                    <h2>
                        <?= htmlspecialchars($orderCount) ?>
                    </h2>

                    <span>
                        Orders received
                    </span>

                </div>

            </div>


        </div>



        <!-- ==============================
         QUICK ACTIONS
         ============================== -->

        <div class="dashboard-section">

            <div class="section-heading">

                <div>

                    <h2>
                        Quick Actions
                    </h2>

                    <p>
                        Manage your CampusKart store quickly.
                    </p>

                </div>

            </div>


            <div class="quick-actions">


                <!-- Manage Products -->

                <a
                    href="product.php"
                    class="action-card">

                    <div class="action-icon">
                        🛒
                    </div>

                    <div>

                        <h3>
                            Manage Products
                        </h3>

                        <p>
                            View, edit and delete products.
                        </p>

                    </div>

                    <span class="arrow">
                        →
                    </span>

                </a>



                <!-- Add Product -->

                <a
                    href="add_product.php"
                    class="action-card">

                    <div class="action-icon">
                        ➕
                    </div>

                    <div>

                        <h3>
                            Add New Product
                        </h3>

                        <p>
                            Add a new product to CampusKart.
                        </p>

                    </div>

                    <span class="arrow">
                        →
                    </span>

                </a>



                <!-- Website -->

                <a
                    href="../index.php"
                    class="action-card">

                    <div class="action-icon">
                        🌐
                    </div>

                    <div>

                        <h3>
                            Visit Website
                        </h3>

                        <p>
                            Open the CampusKart student marketplace.
                        </p>

                    </div>

                    <span class="arrow">
                        →
                    </span>

                </a>


            </div>

        </div>



        <!-- ==============================
         CAMPUSKART INFORMATION
         ============================== -->

        <div class="admin-info">


            <div>

                <span class="info-icon">
                    🎓
                </span>

            </div>


            <div>

                <h2>
                    CampusKart
                </h2>

                <p>
                    A student-focused marketplace designed to make
                    buying college essentials simple, convenient
                    and affordable.
                </p>

            </div>


        </div>


    </section>



    <!-- ==============================
     FOOTER
     ============================== -->

    <footer class="admin-footer">

        <p>
            © 2026 CampusKart
        </p>

        <p>
            Student E-Commerce Platform
        </p>

    </footer>


</body>

</html>