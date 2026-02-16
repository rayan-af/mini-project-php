<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'order') {
        $menu_id = $_POST['menu_id'];
        
        try {
            $db->beginTransaction();

            // 1. Get Recipe
            $stmt = $db->prepare("SELECT inventory_item_id, quantity_needed FROM recipes WHERE menu_item_id = ?");
            $stmt->execute([$menu_id]);
            $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$ingredients) {
                throw new Exception("Recipe not found for this item.");
            }

            // 2. Check Stock for ALL ingredients
            foreach ($ingredients as $ing) {
                $stmt = $db->prepare("SELECT quantity, name FROM inventory WHERE id = ?");
                $stmt->execute([$ing['inventory_item_id']]);
                $stock = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($stock['quantity'] < $ing['quantity_needed']) {
                    throw new Exception("Out of stock: " . $stock['name']);
                }
            }

            // 3. Deduct Stock
            foreach ($ingredients as $ing) {
                $stmt = $db->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ?");
                $stmt->execute([$ing['quantity_needed'], $ing['inventory_item_id']]);
            }

            // 4. Record Order
            $stmt = $db->prepare("INSERT INTO orders (menu_item_id) VALUES (?)");
            $stmt->execute([$menu_id]);

            // 5. Get Name for success message
            $stmt = $db->prepare("SELECT name FROM menu_items WHERE id = ?");
            $stmt->execute([$menu_id]);
            $itemName = $stmt->fetch()['name'];

            $db->commit();
            echo json_encode(['status' => 'success', 'message' => "Ordered $itemName! Stock updated."]);

        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
?>
