<?php

require_once "../config/database.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$result = $conn->query(
    "SELECT * FROM products ORDER BY id DESC"
);

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Products</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header class="admin-nav">

    <h2>CampusKart Admin</h2>

    <nav>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="products.php">
            Products
        </a>

        <a href="add-product.php">
            Add Product
        </a>

    </nav>

</header>


<section class="section">

    <div class="admin-heading">

        <h1>Manage Products</h1>

        <a href="add-product.php" class="btn">
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

            <?php while ($product = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= $product['id'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($product['name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($product['category']) ?>
                    </td>

                    <td>
                        ₹<?= number_format(
                            $product['price'],
                            2
                        ) ?>
                    </td>

                    <td>
                        <?= $product['stock'] ?>
                    </td>

                    <td>

                        <a
                            href="edit-product.php?id=<?= $product['id'] ?>"
                            class="edit"
                        >
                            Edit
                        </a>

                        <a
                            href="delete-product.php?id=<?= $product['id'] ?>"
                            class="delete"
                            onclick="return confirm('Delete this product?')"
                        >
                            Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</section>

</body>
</html>