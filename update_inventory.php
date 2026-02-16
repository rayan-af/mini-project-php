<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_role'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['user_role'];
$user_action = $_POST['action'] ?? '';

// Helper function to log actions
function log_action($db, $role, $action, $item_name) {
    $stmt = $db->prepare("INSERT INTO logs (user_role, action, item_name) VALUES (?, ?, ?)");
    $stmt->execute([$role, $action, $item_name]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // ADD ITEM (Manager Only)
    if ($user_action === 'add' && $role === 'manager') {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];

        $stmt = $db->prepare("INSERT INTO inventory (name, category, quantity, price) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $category, $quantity, $price])) {
            log_action($db, $role, "Added Item", $name);
            header("Location: inventory.php?success=added");
        } else {
            header("Location: inventory.php?error=failed_add");
        }

    // DELETE ITEM (Manager Only)
    } elseif ($user_action === 'delete' && $role === 'manager') {
        $id = $_POST['id'];
        
        // Get name for log
        $stmt = $db->prepare("SELECT name FROM inventory WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        $name = $item['name'] ?? 'Unknown Item';

        $stmt = $db->prepare("DELETE FROM inventory WHERE id = ?");
        if ($stmt->execute([$id])) {
            log_action($db, $role, "Deleted Item", $name);
            header("Location: inventory.php?success=deleted");
        } else {
            header("Location: inventory.php?error=failed_delete");
        }

    // UPDATE QUANTITY (Both Roles)
    } elseif ($user_action === 'update_quantity') {
        $id = $_POST['id'];
        $new_quantity = $_POST['quantity'];

        // Get name for log
        $stmt = $db->prepare("SELECT name FROM inventory WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        $name = $item['name'] ?? 'Unknown Item';

        $stmt = $db->prepare("UPDATE inventory SET quantity = ? WHERE id = ?");
        if ($stmt->execute([$new_quantity, $id])) {
            log_action($db, $role, "Updated Quantity to $new_quantity", $name);
            header("Location: inventory.php?success=updated");
        } else {
            header("Location: inventory.php?error=failed_update");
        }

    } else {
        // Invalid action or permission
        header("Location: inventory.php?error=permission_denied");
    }
} else {
    header("Location: inventory.php");
}
?>
