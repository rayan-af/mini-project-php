<?php
// db.php
try {
    // Create (connect to) SQLite database in file
    $db = new PDO('sqlite:database.sqlite');
    // Set errormode to exceptions
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create tables if they do not exist
    $db->exec("CREATE TABLE IF NOT EXISTS inventory (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        category TEXT NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 0,
        price REAL NOT NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_role TEXT NOT NULL,
        action TEXT NOT NULL,
        item_name TEXT,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

} catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
    exit();
}
?>
