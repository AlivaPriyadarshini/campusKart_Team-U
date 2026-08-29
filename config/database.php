<?php

session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "campuskart";

$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS campuskart");
$conn->select_db($database);

$conn->query("
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(500),
        category VARCHAR(100),
        stock INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL,
        phone VARCHAR(30),
        address TEXT NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL
    )
");

/* Default admin account */
$checkAdmin = $conn->query("SELECT id FROM admins LIMIT 1");

if ($checkAdmin->num_rows == 0) {
    $adminPassword = password_hash("admin123", PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "INSERT INTO admins (username, password) VALUES (?, ?)"
    );

    $username = "admin";
    $stmt->bind_param("ss", $username, $adminPassword);
    $stmt->execute();
}

/* Sample products */
$checkProducts = $conn->query("SELECT id FROM products LIMIT 1");

if ($checkProducts->num_rows == 0) {

    $products = [
        [
            "Campus Backpack",
            "Durable backpack suitable for college students.",
            899,
            "https://images.unsplash.com/photo-1553062407-98eeb64c6a62",
            "Bags",
            20
        ],
        [
            "College Hoodie",
            "Comfortable cotton hoodie for everyday campus life.",
            699,
            "https://images.unsplash.com/photo-1556821840-3a63f95609a7",
            "Fashion",
            15
        ],
        [
            "Wireless Headphones",
            "Comfortable wireless headphones for study and entertainment.",
            1299,
            "https://images.unsplash.com/photo-1505740420928-5e560c06d30e",
            "Electronics",
            10
        ],
        [
            "Study Notebook",
            "Premium ruled notebook for college notes.",
            149,
            "https://images.unsplash.com/photo-1531346680769-a1d79b57de5b",
            "Stationery",
            50
        ]
    ];

    $stmt = $conn->prepare("
        INSERT INTO products
        (name, description, price, image, category, stock)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($products as $product) {
        $stmt->bind_param(
            "ssdssi",
            $product[0],
            $product[1],
            $product[2],
            $product[3],
            $product[4],
            $product[5]
        );

        $stmt->execute();
    }
}
?>