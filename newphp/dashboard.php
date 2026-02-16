<?php
require_once 'db.php';
require_once 'header.php';

// Fetch metrics (Existing)
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM inventory");
    $active_items = $stmt->fetch()['count'];

    $stmt = $db->query("SELECT COUNT(*) as count FROM inventory WHERE quantity < 5");
    $low_stock_count = $stmt->fetch()['count'];

    $stmt = $db->query("SELECT SUM(quantity * price) as val FROM inventory");
    $inventory_val = $stmt->fetch()['val'] ?: 0;

    $stmt = $db->query("SELECT COUNT(*) as count FROM logs WHERE action LIKE 'Waste%'");
    $waste_count = $stmt->fetch()['count'];

    // TRENDING ITEMS (New)
    // Top 3 items in orders table
    $stmt = $db->query("SELECT m.name, COUNT(o.id) as order_count 
                        FROM orders o 
                        JOIN menu_items m ON o.menu_item_id = m.id 
                        GROUP BY o.menu_item_id 
                        ORDER BY order_count DESC 
                        LIMIT 3");
    $trending_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$role = $_SESSION['user_role'];
?>

<h2 class="mb-4">Dashboard</h2>

<div class="row">
    <!-- Active Items -->
    <div class="col-md-6 col-lg-3">
        <div class="card-box">
            <div class="card-title">Active Items</div>
            <div class="card-value"><?php echo $active_items; ?></div>
            <div class="text-muted small">Total in catalog</div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="col-md-6 col-lg-3">
        <div class="card-box">
            <div class="card-title text-amber">Low Stock Alerts</div>
            <div class="card-value text-amber"><?php echo $low_stock_count; ?></div>
            <div class="text-muted small">Items below 5 units</div>
        </div>
    </div>

    <!-- Inventory Value -->
    <div class="col-md-6 col-lg-3">
        <div class="card-box">
            <div class="card-title text-green">Inventory Value</div>
            <div class="card-value text-green">$<?php echo number_format($inventory_val, 2); ?></div>
            <div class="text-muted small">Total asset value</div>
        </div>
    </div>

    <!-- Waste Value (Manager Only) -->
    <?php if ($role === 'manager'): ?>
    <div class="col-md-6 col-lg-3">
        <div class="card-box">
            <div class="card-title text-red">Waste Logs</div>
            <div class="card-value text-red"><?php echo $waste_count; ?></div>
            <div class="text-muted small">Recorded waste events</div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="row">
    <!-- Trending Items Section -->
    <div class="col-md-6">
        <div class="card-box h-100">
            <h5 class="mb-3"><i class="fas fa-fire me-2 text-danger"></i>Trending Items</h5>
            <?php if (count($trending_items) > 0): ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($trending_items as $index => $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary rounded-pill me-2">#<?php echo $index + 1; ?></span>
                            <?php echo htmlspecialchars($item['name']); ?>
                        </div>
                        <span class="badge bg-light text-dark border"><?php echo $item['order_count']; ?> orders</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted text-center py-4">No sales data yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Low Stock Table (Existing) -->
    <div class="col-md-6">
        <div class="card-box h-100">
            <h5 class="mb-3">Low Stock Warnings</h5>
            <?php 
                $stmt = $db->query("SELECT * FROM inventory WHERE quantity < 5 LIMIT 5");
                $low_stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <?php if (count($low_stock_items) > 0): ?>
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($low_stock_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td class="text-danger fw-bold"><?php echo $item['quantity']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted text-center py-4">No items are low on stock.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</div> 
</div> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>
</body>
</html>
