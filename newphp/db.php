<?php
// db.php
try {
    // Create (connect to) SQLite database in file
    $db = new PDO('sqlite:database.sqlite');
    // Set errormode to exceptions
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create tables if they do not exist
    
    // 1. INVENTORY (Existing)
    $db->exec("CREATE TABLE IF NOT EXISTS inventory (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        category TEXT NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 0,
        price REAL NOT NULL
    )");

    // 2. LOGS (Existing)
    $db->exec("CREATE TABLE IF NOT EXISTS logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_role TEXT NOT NULL,
        action TEXT NOT NULL,
        item_name TEXT,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. MENU ITEMS (New)
    $db->exec("CREATE TABLE IF NOT EXISTS menu_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price REAL NOT NULL,
        image_url TEXT
    )");

    // 4. RECIPES (New) - Links Menu Item to Inventory Item
    $db->exec("CREATE TABLE IF NOT EXISTS recipes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        menu_item_id INTEGER NOT NULL,
        inventory_item_id INTEGER NOT NULL,
        quantity_needed INTEGER NOT NULL,
        FOREIGN KEY(menu_item_id) REFERENCES menu_items(id),
        FOREIGN KEY(inventory_item_id) REFERENCES inventory(id)
    )");

    // 5. ORDERS (New)
    $db->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        menu_item_id INTEGER NOT NULL,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(menu_item_id) REFERENCES menu_items(id)
    )");

} catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
    exit();
}
?>
