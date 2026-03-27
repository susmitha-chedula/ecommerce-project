<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// UPDATE
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$quantity, $user_id, $product_id]);

    header("Location: cart.php");
    exit();
}

// REMOVE
if (isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);

    header("Location: cart.php");
    exit();
}

// FETCH
$stmt = $conn->prepare("
SELECT cart.product_id, products.name, products.price, cart.quantity
FROM cart
JOIN products ON cart.product_id = products.id
WHERE cart.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2 style="text-align:center;">Your Cart</h2>

<div style="width: 80%; margin: auto;">

<table style="width:100%; border-collapse: collapse;">
<tr style="background:#2c3e50; color:white;">
    <th style="padding:10px;">Product</th>
    <th style="padding:10px;">Price</th>
    <th style="padding:10px;">Quantity</th>
    <th style="padding:10px;">Subtotal</th>
    <th style="padding:10px;">Action</th>
</tr>

<?php foreach ($cart_items as $item): 
    $subtotal = $item['price'] * $item['quantity'];
    $total += $subtotal;
?>

<tr style="text-align:center; border-bottom:1px solid #ddd;">
    <td style="padding:10px;"><?= $item['name']; ?></td>
    <td>₹<?= $item['price']; ?></td>

    <td>
        <form method="POST">
            <input type="hidden" name="product_id" value="<?= $item['product_id']; ?>">
            <input type="number" name="quantity" value="<?= $item['quantity']; ?>" style="width:60px;">
            <button name="update_quantity">Update</button>
        </form>
    </td>

    <td>₹<?= $subtotal; ?></td>

    <td>
        <form method="POST">
            <input type="hidden" name="product_id" value="<?= $item['product_id']; ?>">
            <button name="remove_from_cart">Remove</button>
        </form>
    </td>
</tr>

<?php endforeach; ?>

</table>

<h3 style="text-align:right; margin-top:20px;">Total: ₹<?= $total; ?></h3>

<br>
<a href="../index.php">← Back to Shop</a>

</div>

</body>
</html>