<?php
require_once 'db.php';
require_once 'header.php';

// Fetch verification logs
$stmt = $db->query("SELECT * FROM logs ORDER BY timestamp DESC");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Activity Logs</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Time</th>
            <th>Role</th>
            <th>Action</th>
            <th>Item Affected</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?php echo $log['timestamp']; ?></td>
                <td><span class="badge bg-<?php echo ($log['user_role'] == 'manager' ? 'danger' : 'info'); ?>"><?php echo ucfirst($log['user_role']); ?></span></td>
                <td><?php echo htmlspecialchars($log['action']); ?></td>
                <td><?php echo htmlspecialchars($log['item_name']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
