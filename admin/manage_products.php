<?php
session_start();
include '../includes/db.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Delete product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$id]);
}

// Fetch products
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background: white;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background: green;
            color: white;
        }
        img {
            width: 50px;
        }
        a {
            color: red;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Manage Products</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Image</th>
            <th>Action</th>
        </tr>

        <?php foreach ($products as $product): ?>
        <tr>
            <td><?= $product['id']; ?></td>
            <td><?= htmlspecialchars($product['name']); ?></td>
            <td>₹<?= $product['price']; ?></td>
            <td><?= htmlspecialchars($product['description']); ?></td>
            <td>
                <img src="../images/<?= $product['image']; ?>">
            </td>
            <td>
                <a href="?delete=<?= $product['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>

    </table>

    <br>
    <a href="dashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>