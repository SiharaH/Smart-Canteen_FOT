// Dark Mode Toggle
const toggleBtn = document.getElementById('theme-toggle');
toggleBtn.addEventListener('click', () => {
    document.body.classList.toggle('dark');
    toggleBtn.textContent = document.body.classList.contains('dark') ? '☀️ Light Mode' : '🌙 Dark Mode';
});

// Load Dashboard Stats
function loadDashboardStats() {
    fetch('charts_data.php')
    .then(res => res.json())
    .then(data => {
        document.getElementById('pending-count').textContent = data.pending;
        document.getElementById('preparing-count').textContent = data.preparing;
        document.getElementById('ready-count').textContent = data.ready;
        document.getElementById('completed-count').textContent = data.completed;
        document.getElementById('total-count').textContent = data.total;

        // Orders Status Chart
        const ctx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Preparing', 'Ready', 'Completed'],
                datasets: [{
                    data: [data.pending, data.preparing, data.ready, data.completed],
                    backgroundColor: ['#e67e22','#f1c40f','#3498db','#2ecc71']
                }]
            }
        });

        // Daily Revenue Chart
        const revCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: data.daily_labels,
                datasets: [{
                    label: 'Revenue (LKR)',
                    data: data.daily_revenue,
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    borderColor: '#2980b9',
                    fill: true,
                    tension: 0.3
                }]
            }
        });
    });
}

// Low Stock Alerts
function loadLowStockAlerts() {
    fetch('inventory_data.php')
    .then(res => res.json())
    .then(data => {
        const ul = document.getElementById('low-stock-list');
        ul.innerHTML = '';
        data.forEach(item => {
            if(item.stock_quantity <= item.low_stock_threshold){
                ul.innerHTML += `<li>⚠️ ${item.item_name} - ${item.stock_quantity} ${item.unit} left</li>`;
            }
        });
    });
}
let lastCheck = new Date().toISOString().slice(0, 19); // current timestamp

function checkNewOrders(){
    fetch(`new_orders.php?last=${lastCheck}`)
    .then(res => res.json())
    .then(data => {
        if(data.length > 0){
            data.forEach(order => {
                showToast(`New Order: ${order.customer_name} ordered ${order.quantity} x ${order.item_name}`);
            });
            // Update lastCheck to latest order_time
            lastCheck = new Date().toISOString().slice(0, 19);
        }
    })
    .catch(err => console.error(err));
}

// Show toast function
function showToast(message){
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => container.removeChild(toast), 5000);
}



function loadLowStockAlerts() {
    fetch('inventory_data.php')
    .then(res => res.json())
    .then(data => {
        const ul = document.getElementById('low-stock-list');
        ul.innerHTML = '';
        data.forEach(item => {
            ul.innerHTML += `<li>⚠️ ${item.item_name} - ${item.stock_quantity} ${item.unit} left</li>`;
        });
    });
}

// Load initially & refresh every 10 seconds
loadLowStockAlerts();
setInterval(loadLowStockAlerts, 10000);


// Check new orders every 5 seconds
setInterval(checkNewOrders, 5000);

// Auto Refresh
loadDashboardStats();
loadLowStockAlerts();
setInterval(() => { loadDashboardStats(); loadLowStockAlerts(); }, 10000);
