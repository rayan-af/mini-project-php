<?php
require_once 'db.php';
require_once 'header.php';

$role = $_SESSION['user_role'];

// Handle Waste Log Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'log_waste') {
    $name = $_POST['item_name'];
    $reason = $_POST['reason'];
    $qty = $_POST['quantity'];
    
    // Log as 'waste' action
    $logStmt = $db->prepare("INSERT INTO logs (user_role, action, item_name) VALUES (?, ?, ?)");
    $logStmt->execute([$role, "Waste: $qty x $name ($reason)", $name]);
    
    $success = "Waste logged successfully.";
}

// Fetch recent waste logs
$stmt = $db->prepare("SELECT * FROM logs WHERE action LIKE 'Waste%' ORDER BY timestamp DESC LIMIT 20");
$stmt->execute();
$waste_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch items for dropdown
$itemsStmt = $db->query("SELECT name FROM inventory ORDER BY name ASC");
$items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="mb-4">Waste Log</h2>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card-box">
            <h5 class="mb-3">Log Spoilage / Waste</h5>
            <form method="POST">
                <input type="hidden" name="action" value="log_waste">
                <div class="mb-3">
                    <label>Item</label>
                    <select name="item_name" class="form-select" required>
                        <option value="">Select Item...</option>
                        <?php foreach($items as $i): ?>
                            <option value="<?php echo htmlspecialchars($i['name']); ?>"><?php echo htmlspecialchars($i['name']); ?></option>
                        <?php endforeach; ?>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Quantity Wasted</label>
                    <input type="number" name="quantity" class="form-control" required min="1">
                </div>
                <div class="mb-3">
                    <label>Reason</label>
                    <select name="reason" class="form-select">
                        <option value="Expired">Expired</option>
                        <option value="Dropped/Spilled">Dropped/Spilled</option>
                        <option value="Incorrect Order">Incorrect Order</option>
                        <option value="Quality Issue">Quality Issue</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger w-100">Log Waste</button>
            </form>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card-box">
            <h5 class="mb-3">Recent Waste Logs</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($waste_logs) > 0): ?>
                        <?php foreach ($waste_logs as $log): ?>
                        <tr>
                            <td><?php echo date('M d, H:i', strtotime($log['timestamp'])); ?></td>
                            <td><?php echo ucfirst($log['user_role']); ?></td>
                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center text-muted">No waste recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>
</body>
</html>
