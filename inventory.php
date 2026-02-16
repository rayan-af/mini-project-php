<?php
require_once 'db.php';
require_once 'header.php';

$role = $_SESSION['user_role'];

// Handle Quick Update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'quick_update') {
    $id = $_POST['id'];
    $qty = $_POST['quantity'];
    
    // Log previous qty for audit (omitted for brevity, just update)
    $stmt = $db->prepare("UPDATE inventory SET quantity = ? WHERE id = ?");
    $stmt->execute([$qty, $id]);
    
    // Log action
    $stmt = $db->prepare("SELECT name FROM inventory WHERE id = ?");
    $stmt->execute([$id]);
    $name = $stmt->fetch()['name'];
    
    $logStmt = $db->prepare("INSERT INTO logs (user_role, action, item_name) VALUES (?, ?, ?)");
    $logStmt->execute([$role, "Quick Update to $qty", $name]);
    
    // Refresh to show update
    echo "<script>window.location.href='inventory.php';</script>";
    exit();
}

$stmt = $db->query("SELECT * FROM inventory ORDER BY name ASC");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Inventory Listing</h2>
    <?php if ($role === 'manager'): ?>
        <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="fas fa-plus me-2"></i>New Item
        </button>
    <?php endif; ?>
</div>

<div class="card-box">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th style="width: 150px;">Quick Quantity</th>
                    <?php if ($role === 'manager'): ?>
                        <th class="text-end">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <?php 
                        $statusClass = ($item['quantity'] < 5) ? 'bg-low' : 'bg-good';
                        $statusText = ($item['quantity'] < 5) ? 'Low Stock' : 'In Stock';
                        // Icon mapping based on category (Produce, Dairy, etc. - simple check)
                        $cat = strtolower($item['category']);
                        $icon = 'fa-box'; 
                        if (strpos($cat, 'pro') !== false) $icon = 'fa-carrot';
                        if (strpos($cat, 'dai') !== false) $icon = 'fa-cheese';
                        if (strpos($cat, 'bev') !== false) $icon = 'fa-wine-bottle';
                    ?>
                <tr>
                    <td class="fw-bold">
                        <i class="fas <?php echo $icon; ?> me-2 text-muted"></i>
                        <?php echo htmlspecialchars($item['name']); ?>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($item['category']); ?></span>
                    </td>
                    <td>
                        <span class="badge-stock <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                    </td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>
                        <form method="POST" class="d-flex">
                            <input type="hidden" name="action" value="quick_update">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <input type="number" name="quantity" class="form-control form-control-sm me-1" value="<?php echo $item['quantity']; ?>" min="0">
                            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fas fa-check"></i></button>
                        </form>
                    </td>
                    <?php if ($role === 'manager'): ?>
                        <td class="text-end">
                            <form action="update_inventory.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this item?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Item Modal (Reused) -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="update_inventory.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label>Item Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Category (Produce, Dairy, Apps, etc.)</label>
                        <input type="text" name="category" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
<!-- End Page Content Wrapper from header.php -->
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>
</body>
</html>
