<?php
require_once 'includes/init.php';

header('Content-Type: application/json');

// Initialize Cart class
$cart = new Cart($db);

$action = $_POST['action'] ?? '';
$productId = $_POST['product_id'] ?? 0;

switch ($action) {
    case 'add':
        $quantity = $_POST['quantity'] ?? 1;
        $result = $cart->addItem($productId, $quantity);
        echo json_encode($result);
        break;

    case 'update':
        $quantity = $_POST['quantity'] ?? 1;
        $result = $cart->updateQuantity($productId, $quantity);
        echo json_encode($result);
        break;

    case 'remove':
        $result = $cart->removeItem($productId);
        echo json_encode($result);
        break;

    case 'clear':
        $cart->clear();
        echo json_encode(['success' => true, 'message' => 'Cart cleared']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
