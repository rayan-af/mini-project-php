<?php
require_once 'db.php';
require_once 'header.php';

// Fetch Menu Items with Stock Status
// We need to know if an item is available based on its recipe
$stmt = $db->query("SELECT * FROM menu_items");
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

function checkAvailability($db, $menu_id) {
    $stmt = $db->prepare("SELECT r.quantity_needed, i.quantity 
                          FROM recipes r 
                          JOIN inventory i ON r.inventory_item_id = i.id 
                          WHERE r.menu_item_id = ?");
    $stmt->execute([$menu_id]);
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($ingredients as $ing) {
        if ($ing['quantity'] < $ing['quantity_needed']) {
            return false; 
        }
    }
    return true;
}
?>

<h2 class="mb-4"><i class="fas fa-cash-register me-2"></i>Point of Sale</h2>

<div class="row" id="menu-grid">
    <?php foreach ($menu_items as $item): 
        $isAvailable = checkAvailability($db, $item['id']);
        $cardClass = $isAvailable ? 'border-success' : 'border-danger opacity-75';
        $btnClass = $isAvailable ? 'btn-success' : 'btn-secondary disabled';
        $btnText = $isAvailable ? 'Order Now' : 'Out of Stock';
    ?>
    <div class="col-md-4 col-lg-3 mb-4">
        <div class="card h-100 <?php echo $cardClass; ?> shadow-sm">
            <div class="card-body text-center d-flex flex-column">
                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($item['name']); ?></h5>
                <h6 class="card-subtitle mb-2 text-muted">$<?php echo number_format($item['price'], 2); ?></h6>
                <div class="mt-auto">
                    <button class="btn <?php echo $btnClass; ?> w-100 order-btn" 
                            data-id="<?php echo $item['id']; ?>" 
                            <?php echo $isAvailable ? '' : 'disabled'; ?>>
                        <i class="fas fa-mug-hot me-1"></i> <?php echo $btnText; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <strong class="me-auto" id="toast-title">Notification</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="toast-message">
      Operation successful.
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toastEl = document.getElementById('liveToast');
    const toast = new bootstrap.Toast(toastEl);
    const toastTitle = document.getElementById('toast-title');
    const toastMessage = document.getElementById('toast-message');

    document.querySelectorAll('.order-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const menuId = this.dataset.id;
            const btn = this; // ref

            // Disable button temporarily
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            const formData = new FormData();
            formData.append('action', 'order');
            formData.append('menu_id', menuId);

            fetch('pos_backend.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    toastTitle.innerText = 'Order Placed';
                    toastTitle.className = 'me-auto text-success';
                    toastMessage.innerText = data.message;
                    toast.show();
                    // Optional: Reload page to update stock calculation after short delay
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastTitle.innerText = 'Error';
                    toastTitle.className = 'me-auto text-danger';
                    toastMessage.innerText = data.message;
                    toast.show();
                    btn.disabled = false;
                    btn.innerHTML = 'Order Now';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = 'Order Now';
            });
        });
    });
});
</script>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>
</body>
</html>
