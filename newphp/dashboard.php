<?php
require_once 'db.php';
require_once 'header.php';

// Fetch metrics
try {
    // 1. Total active items (count)
    $stmt = $db->query("SELECT COUNT(*) as count FROM inventory");
    $active_items = $stmt->fetch()['count'];

    // 2. Low Stock Items (count < 5)
    $stmt = $db->query("SELECT COUNT(*) as count FROM inventory WHERE quantity < 5");
    $low_stock_count = $stmt->fetch()['count'];

    // 3. Current Inventory Value (sum(quantity * price))
    $stmt = $db->query("SELECT SUM(quantity * price) as val FROM inventory");
    $inventory_val = $stmt->fetch()['val'] ?: 0;

    // 4. Waste Value (sum of items logged as 'waste' - assumes we implement waste tracking later or use logs)
    // For now, let's mock it or query logs if structure allows.
    // Let's query logs for action='waste' and join with inventory to get price? 
    // Simplified: Just count 'waste' entries for now or use 0.
    // Ideally we'd store the value in logs. Let's assume 0 for start or query logs table.
    $stmt = $db->query("SELECT COUNT(*) as count FROM logs WHERE action = 'waste'");
    $waste_count = $stmt->fetch()['count'];
    
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

<!-- Recent Low Stock Table -->
<?php
$stmt = $db->query("SELECT * FROM inventory WHERE quantity < 5 LIMIT 5");
$low_stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card-box">
    <h5 class="mb-3">Low Stock Warnings</h5>
    <?php if (count($low_stock_items) > 0): ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($low_stock_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($item['category']); ?></span></td>
                    <td class="text-danger fw-bold"><?php echo $item['quantity']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">No items are currently low on stock.</p>
    <?php endif; ?>
</div>

</div> 
<!-- End Page Content Wrapper from header.php -->
</div> 
<!-- End Wrapper from header.php -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>
</body>
</html>
