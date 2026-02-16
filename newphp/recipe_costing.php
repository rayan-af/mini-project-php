<?php
require_once 'header.php';
?>

<h2 class="mb-4">Recipe Costing Calculator</h2>

<div class="row">
    <div class="col-md-6">
        <div class="card-box">
            <h5 class="mb-4 text-muted border-bottom pb-2">Plate Inputs</h5>
            <div class="mb-3">
                <label for="plateCost" class="form-label">Total Plate Cost ($)</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="plateCost" step="0.01" placeholder="0.00">
                </div>
                <div class="form-text">Cost of ingredients per serving.</div>
            </div>
            
            <div class="mb-3">
                <label for="salesPrice" class="form-label">Target Sales Price ($)</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="salesPrice" step="0.01" placeholder="0.00">
                </div>
                <div class="form-text">Menu price for the customer.</div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-box h-100 d-flex flex-column justify-content-center align-items-center text-center">
            <h5 class="mb-3 text-muted">Gross Profit Margin</h5>
            <div class="display-4 fw-bold text-success" id="marginOutput">0.00%</div>
            <p class="mt-3 text-muted">Profit per Plate: <span id="profitOutput" class="fw-bold text-dark">$0.00</span></p>
        </div>
    </div>
</div>

</div>
<!-- End Page Content Wrapper from header.php -->
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>
<script>
    // Just in case we need specific inline logic or re-bind
</script>
</body>
</html>
