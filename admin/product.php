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

    <title>Manage Products</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>


<body>


<header class="admin-nav">

    <h2>CampusKart Admin</h2>


    <nav>

        <a href="dashboard.php">
            Dashboard
        </a>


        <!-- Correct filename -->
        <a href="product.php">
            Products
        </a>


        <!-- Correct filename -->
        <a href="add_product.php">
            Add Product
        </a>

    </nav>

</header>


<section class="section">


    <div class="admin-heading">

        <h1>Manage Products</h1>


        <a
            href="add_product.php"
            class="btn"
        >
            + Add Product
        </a>

    </div>


    <div class="table-container">


        <table>


            <thead>

                <tr>

                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Action</th>

                </tr>

            </thead>


            <tbody>


            <?php if ($result->num_rows > 0): ?>


                <?php while ($product = $result->fetch_assoc()): ?>


                    <tr>


                        <td>
                            <?= (int)$product['id'] ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $product['name']
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $product['category']
                            ) ?>
                        </td>


                        <td>
                            ₹<?= number_format(
                                (float)$product['price'],
                                2
                            ) ?>
                        </td>


                        <td>
                            <?= (int)$product['stock'] ?>
                        </td>


                        <td>


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


                        </td>


                    </tr>


                <?php endwhile; ?>


            <?php else: ?>


                <tr>

                    <td colspan="6">
                        No products found.
                    </td>

                </tr>


            <?php endif; ?>


            </tbody>


        </table>


    </div>


</section>


</body>

</html>