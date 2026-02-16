<?php
require_once 'db.php';

try {
    $db->beginTransaction();

    // 1. Clear existing data to avoid duplicates (optional, but good for reliable seeding)
    // In a real app we might check existence, but for this "Mission" restart:
    $db->exec("DELETE FROM menu_items");
    $db->exec("DELETE FROM recipes");
    // We keep inventory/logs/orders to not wipe history unless requested, 
    // but for the demo we need specific ingredients. Let's Ensure they exist.

    // Helper to get or create inventory item
    function getInventoryId($db, $name, $col, $qty) {
        $stmt = $db->prepare("SELECT id FROM inventory WHERE name = ?");
        $stmt->execute([$name]);
        $res = $stmt->fetch();
        if ($res) return $res['id'];
        
        $stmt = $db->prepare("INSERT INTO inventory (name, category, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $col, $qty, 0.00]); // Price 0 for ingredients cost
        return $db->lastInsertId();
    }

    // INGREDIENTS (Units arbitrary, say 1 unit = 1 shot or 100ml)
    $coffee_bean_id = getInventoryId($db, "Espresso Beans (Shots)", "Ingredient", 500);
    $milk_id = getInventoryId($db, "Whole Milk (100ml)", "Ingredient", 200);
    $caramel_id = getInventoryId($db, "Caramel Syrup (Pumps)", "Ingredient", 100);
    $vanilla_id = getInventoryId($db, "Vanilla Syrup (Pumps)", "Ingredient", 100);
    $cold_brew_conc_id = getInventoryId($db, "Cold Brew Concentrate (100ml)", "Ingredient", 100);

    // MENU ITEMS
    $menu = [
        ['name' => 'Caffè Latte', 'price' => 4.50, 'recipe' => [
            $coffee_bean_id => 1, // 1 shot
            $milk_id => 3         // 300ml
        ]],
        ['name' => 'Cappuccino', 'price' => 4.50, 'recipe' => [
            $coffee_bean_id => 1,
            $milk_id => 2
        ]],
        ['name' => 'Caramel Macchiato', 'price' => 5.25, 'recipe' => [
            $coffee_bean_id => 2,
            $milk_id => 2,
            $vanilla_id => 2,
            $caramel_id => 1
        ]],
        ['name' => 'Flat White', 'price' => 4.75, 'recipe' => [
            $coffee_bean_id => 2, // Ristretto shots usually, let's say 2 units
            $milk_id => 2
        ]],
        ['name' => 'Cold Brew', 'price' => 4.25, 'recipe' => [
            $cold_brew_conc_id => 2,
            $milk_id => 1 // Splash
        ]]
    ];

    foreach ($menu as $item) {
        $stmt = $db->prepare("INSERT INTO menu_items (name, price) VALUES (?, ?)");
        $stmt->execute([$item['name'], $item['price']]);
        $menu_id = $db->lastInsertId();

        foreach ($item['recipe'] as $ing_id => $qty) {
            $rParams = [$menu_id, $ing_id, $qty];
            $db->prepare("INSERT INTO recipes (menu_item_id, inventory_item_id, quantity_needed) VALUES (?, ?, ?)")
               ->execute($rParams);
        }
    }

    $db->commit();
    echo "Database seeded with Starbucks menu and ingredients successfully.";

} catch (Exception $e) {
    $db->rollBack();
    echo "Error seeding database: " . $e->getMessage();
}
?>
