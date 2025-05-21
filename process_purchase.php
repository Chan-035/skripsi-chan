<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menuID = $_POST['menuID'];
    $quantity = $_POST['quantity'];

    // Get menu details
    $query = "SELECT * FROM menu WHERE menuID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $menuID);
    $stmt->execute();
    $menuResult = $stmt->get_result();
    $menu = $menuResult->fetch_assoc();

    if ($menu) {
        // Calculate total price
        $totalPrice = $menu['harga'] * $quantity;

        // Insert into orders table (you would need to adjust this query for your system)
        $orderQuery = "INSERT INTO pesanan (menuID, quantity, totalPrice, orderDate) VALUES (?, ?, ?, NOW())";
        $orderStmt = $conn->prepare($orderQuery);
        $orderStmt->bind_param("sid", $menuID, $quantity, $totalPrice);
        $orderStmt->execute();

        echo "Order placed successfully!";
    } else {
        echo "Menu item not found!";
    }
} else {
    echo "Invalid request!";
}
?>
