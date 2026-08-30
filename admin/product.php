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
   GET PRODUCTS
   ========================================== */

$result = $conn->query(
    "SELECT * FROM products ORDER BY id DESC"
);

if (!$result) {
    die("Unable to load products: " . $conn->error);
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Products - CampusKart</title>

    <link
        rel="stylesheet"
        href="../css/product2.css"
    >

</head>


<body>


<!-- ==========================================
     ADMIN NAVBAR
     ========================================== -->

<header class="admin-nav">


    <div class="admin-brand">

        <div class="admin-brand">
            <div class="admin-logo">
                <img src="../image/WhatsApp Image 2026-08-29 at 12.40.25 PM.jpeg"
                    alt="CampusKart Logo">
            </div>
        </div>

        <div>

            <h2>
                CampusKart
            </h2>

            <span>
                Admin Panel
            </span>

        </div>

    </div>


    <nav>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a
            href="product.php"
            class="active"
        >
            Products
        </a>

        <a href="add_product.php">
            Add Product
        </a>

        <a href="../index.php">
            Website
        </a>

        <a
            href="login.php?logout=1"
            class="logout"
        >
            Logout
        </a>

    </nav>


</header>



<!-- ==========================================
     MAIN CONTENT
     ========================================== -->

<section class="product-admin">


    <!-- Page Heading -->

    <div class="page-heading">


        <div>

            <p class="page-label">
                INVENTORY MANAGEMENT
            </p>

            <h1>
                Manage Products
            </h1>

            <p>
                View, edit and manage all products available
                on the CampusKart marketplace.
            </p>

        </div>


        <a
            href="add_product.php"
            class="add-product-btn"
        >
            + Add Product
        </a>


    </div>



    <!-- ==========================================
         PRODUCT TABLE
         ========================================== -->

    <div class="product-table-card">


        <div class="table-header">

            <div>

                <h2>
                    All Products
                </h2>

                <p>
                    Manage your current product inventory.
                </p>

            </div>

        </div>


        <div class="table-container">


            <table>


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Product
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Price
                        </th>

                        <th>
                            Stock
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php if ($result->num_rows > 0): ?>


                    <?php while ($product = $result->fetch_assoc()): ?>


                        <tr>


                            <!-- Product ID -->

                            <td>

                                <span class="product-id">

                                    #<?= (int)$product['id'] ?>

                                </span>

                            </td>



                            <!-- Product Name -->

                            <td>

                                <div class="product-name">

                                    <div class="product-icon">
                                        🛍️
                                    </div>

                                    <div>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $product['name']
                                            ) ?>
                                        </strong>

                                        <span>
                                            CampusKart Product
                                        </span>

                                    </div>

                                </div>

                            </td>



                            <!-- Category -->

                            <td>

                                <span class="category">

                                    <?= htmlspecialchars(
                                        $product['category']
                                    ) ?>

                                </span>

                            </td>



                            <!-- Price -->

                            <td>

                                <strong class="product-price">

                                    ₹<?= number_format(
                                        (float)$product['price'],
                                        2
                                    ) ?>

                                </strong>

                            </td>



                            <!-- Stock -->

                            <td>

                                <?php if ((int)$product['stock'] > 0): ?>

                                    <span class="stock available">
                                        <?= (int)$product['stock'] ?>
                                        Available
                                    </span>

                                <?php else: ?>

                                    <span class="stock unavailable">
                                        Out of Stock
                                    </span>

                                <?php endif; ?>

                            </td>



                            <!-- Actions -->

                            <td>

                                <div class="product-actions">


                                    <!-- Edit -->

                                    <a
                                        href="edit_product.php?id=<?= (int)$product['id'] ?>"
                                        class="edit"
                                    >
                                        Edit
                                    </a>


                                    <!-- Delete -->

                                    <a
                                        href="delete_product.php?id=<?= (int)$product['id'] ?>"
                                        class="delete"
                                        onclick="return confirm('Delete this product?')"
                                    >
                                        Delete
                                    </a>


                                </div>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="6"
                            class="no-products"
                        >

                            <div class="empty-products">

                                <div class="empty-icon">
                                    📦
                                </div>

                                <h3>
                                    No Products Found
                                </h3>

                                <p>
                                    There are currently no products
                                    in your CampusKart inventory.
                                </p>

                                <a
                                    href="add_product.php"
                                    class="add-product-btn"
                                >
                                    + Add Your First Product
                                </a>

                            </div>

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>


            </table>


        </div>


    </div>


    <!-- ==========================================
         INFORMATION BOX
         ========================================== -->

    <div class="product-info-box">


        <div class="info-icon">
            💡
        </div>


        <div>

            <h3>
                Product Management
            </h3>

            <p>
                Keep your CampusKart inventory updated.
                You can edit product information or remove
                products that are no longer available.
            </p>

        </div>


    </div>


</section>



<!-- ==========================================
     FOOTER
     ========================================== -->

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