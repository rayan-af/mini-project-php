document.addEventListener('DOMContentLoaded', function () {
    // Sidebar Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const wrapper = document.getElementById('wrapper');

    if (menuToggle) {
        menuToggle.addEventListener('click', function (e) {
            e.preventDefault();
            wrapper.classList.toggle('toggled');
        });
    }

    // Recipe Costing Calculator
    const plateCostInput = document.getElementById('plateCost');
    const salesPriceInput = document.getElementById('salesPrice');
    const marginOutput = document.getElementById('marginOutput');
    const profitOutput = document.getElementById('profitOutput');

    function calculateMargin() {
        if (!plateCostInput || !salesPriceInput) return;

        const cost = parseFloat(plateCostInput.value) || 0;
        const price = parseFloat(salesPriceInput.value) || 0;

        if (price > 0) {
            const profit = price - cost;
            const margin = (profit / price) * 100;

            marginOutput.innerText = margin.toFixed(2) + '%';
            profitOutput.innerText = '$' + profit.toFixed(2);

            // Visual feedback
            if (margin < 20) {
                marginOutput.className = 'display-4 fw-bold text-danger';
            } else if (margin < 30) {
                marginOutput.className = 'display-4 fw-bold text-warning';
            } else {
                marginOutput.className = 'display-4 fw-bold text-success';
            }
        } else {
            marginOutput.innerText = '0.00%';
            profitOutput.innerText = '$0.00';
        }
    }

    if (plateCostInput && salesPriceInput) {
        plateCostInput.addEventListener('input', calculateMargin);
        salesPriceInput.addEventListener('input', calculateMargin);
    }
});
