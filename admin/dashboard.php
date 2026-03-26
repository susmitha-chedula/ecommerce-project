<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f4f4;
        }
        .container {
            width: 60%;
            margin: 50px auto;
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
        }
        a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background: green;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout {
            background: red;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Admin Dashboard</h2>

    <a href="add_product.php">Add Product</a>
    <a href="manage_products.php">Manage Products</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

</body>
</html>