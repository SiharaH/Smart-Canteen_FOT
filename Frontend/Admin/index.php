<?php
$mysqli = new mysqli("localhost", "root", "1234", "canteen");
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

// Order counts by status
$statuses = ['pending', 'preparing', 'ready', 'completed'];
$order_counts = [];
foreach ($statuses as $status) {
    $res = $mysqli->query("SELECT COUNT(*) as count FROM orders WHERE status='$status'");
    $order_counts[$status] = $res->fetch_assoc()['count'];
}



// Total orders
$res_total = $mysqli->query("SELECT COUNT(*) as count FROM orders");
$total_orders = $res_total->fetch_assoc()['count'];

// Menu popularity
$menu_res = $mysqli->query("SELECT m.item_name, SUM(o.quantity) as total_qty 
                            FROM orders o 
                            JOIN menu m ON o.item_id = m.id 
                            GROUP BY o.item_id 
                            ORDER BY total_qty DESC");
$menu_labels = [];
$menu_data = [];
while($row = $menu_res->fetch_assoc()){
    $menu_labels[] = $row['item_name'];
    $menu_data[] = $row['total_qty'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🍽️ Canteen Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root {
    --bg-color: #f4f7f9; --text-color: #333; --card-bg: #fff;
    --navbar-bg: #1abc9c; --navbar-text: #fff;
}
body { margin:0; font-family:'Roboto',sans-serif; background:var(--bg-color); color:var(--text-color);}
.navbar, header { display:flex; justify-content:space-between; align-items:center; padding:15px 20px; background:var(--navbar-bg); color:var(--navbar-text);}
.navbar .nav-link { margin:0 10px; text-decoration:none; color:var(--navbar-text); font-weight:500;}
.navbar .nav-link.active, .navbar .nav-link:hover { color:#f1c40f;}
.cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:20px; margin:20px 0;}
.card { background:var(--card-bg); padding:20px; border-radius:10px; text-align:center; box-shadow:0 5px 15px rgba(0,0,0,0.1); transition:0.3s;}
.card:hover { transform:translateY(-5px); box-shadow:0 8px 20px rgba(0,0,0,0.15);}
.card.pending { background:#f1c40f; color:#fff;}
.card.preparing { background:#3498db; color:#fff;}
.card.ready { background:#e67e22; color:#fff;}
.card.completed { background:#2ecc71; color:#fff;}
.card.total { background:#9b59b6; color:#fff;}
.charts { display:flex; flex-wrap:wrap; gap:30px; justify-content:center; margin-top:30px;}
.chart-container { background:var(--card-bg); padding:20px; border-radius:10px; flex:1 1 500px; box-shadow:0 5px 15px rgba(0,0,0,0.1);}
.toast { background:#1abc9c; color:#fff; padding:15px 20px; margin-bottom:10px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.2); animation: slideIn 0.5s, fadeOut 0.5s 4.5s forwards; font-weight:500;}
@keyframes slideIn {0%{opacity:0; transform:translateX(100%);}100%{opacity:1; transform:translateX(0);}}
@keyframes fadeOut {to{opacity:0; transform:translateX(100%);}}
.inventory-alerts { margin:20px 0; background:#fff3cd; color:#856404; padding:15px; border-radius:8px; border:1px solid #ffeeba;}
.inventory-alerts ul { list-style:none; padding-left:0;}
body.dark { --bg-color:#1e1e2f; --text-color:#f4f4f4; --card-bg:#2c2c3c; --navbar-bg:#111127; --navbar-text:#f4f4f4;}
body.dark .toast { background:#3498db; color:#fff;}
@media(max-width:768px){.cards{grid-template-columns:repeat(auto-fit,minmax(140px,1fr));}.charts{flex-direction:column;gap:20px;}.navbar{flex-direction:column;align-items:flex-start;}.navbar-center{flex-wrap:wrap;margin-top:10px;}}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-left">
        <a href="index.php" class="brand">🍴 Canteen Admin</a>
        <span class="menu-toggle">☰</span>
    </div>
    <nav class="navbar">
  <ul>
    <li><a href="index.php">Dashboard</a></li>
    <li><a href="orders.php">Orders</a></li>
    <li><a href="inventory.php">Inventory</a></li>
    <li><a href="sales.php">Payments</a></li>
    <li><a href="Menu-Management/menu_add.php">Menu</a></li>
    <li><a href="activity_logs.php">Activity Logs</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</nav>

    <div class="navbar-right">
        <button id="theme-toggle">🌙 Dark Mode</button>
        <a href="logout.php" class="nav-link logout">Logout</a>
    </div>
</nav>

<div class="container">
    <!-- Cards -->
    <div class="cards">
        <div class="card pending"><h3><?= $order_counts['pending'] ?></h3><p>Pending Orders</p></div>
        <div class="card preparing"><h3><?= $order_counts['preparing'] ?></h3><p>Preparing Orders</p></div>
        <div class="card ready"><h3><?= $order_counts['ready'] ?></h3><p>Ready Orders</p></div>
        <div class="card completed"><h3><?= $order_counts['completed'] ?></h3><p>Completed Orders</p></div>
        <div class="card total"><h3><?= $total_orders ?></h3><p>Total Orders</p></div>
    </div>

    <!-- Charts -->
    <div class="charts">
        <div class="chart-container">
            <h3 style="text-align:center;">Order Status Distribution</h3>
            <canvas id="statusPie"></canvas>
        </div>
        <div class="chart-container">
            <h3 style="text-align:center;">Most Popular Menu Items</h3>
            <canvas id="menuBar"></canvas>
        </div>
    </div>

    <!-- Daily Revenue & Payment History -->
    <div class="container">
        <h2>💰 Daily Revenue: <span id="daily-revenue">0.00</span> LKR</h2>
        <h3>Payment History</h3>
        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody id="payments-body"></tbody>
        </table>
    </div>

    <!-- Low Stock -->
    <section class="inventory-alerts">
        <h3>🧾 Low Stock Alerts</h3>
        <ul id="low-stock-list"></ul>
    </section>
</div>

<div id="toast-container" style="position:fixed; top:80px; right:20px; z-index:9999;"></div>

<script>
// Charts
new Chart(document.getElementById('statusPie'), {
    type:'pie', data:{labels:['Pending','Preparing','Ready','Completed'], datasets:[{data:[<?= $order_counts['pending'] ?>,<?= $order_counts['preparing'] ?>,<?= $order_counts['ready'] ?>,<?= $order_counts['completed'] ?>], backgroundColor:['#f1c40f','#3498db','#e67e22','#2ecc71']}]}, options:{responsive:true, plugins:{legend:{position:'bottom'}}}
});
new Chart(document.getElementById('menuBar'), {
    type:'bar', data:{labels:<?= json_encode($menu_labels) ?>, datasets:[{label:'Quantity Ordered', data:<?= json_encode($menu_data) ?>, backgroundColor:'#1abc9c'}]}, options:{responsive:true, scales:{y:{beginAtZero:true}}}
});

// Dark/Light Mode
const themeToggle=document.getElementById('theme-toggle');
themeToggle.addEventListener('click',()=>{document.body.classList.toggle('dark'); themeToggle.textContent=document.body.classList.contains('dark')?'☀️ Light Mode':'🌙 Dark Mode';});

// Toast notifications
let lastCheck=new Date().toISOString().slice(0,19);
function checkNewOrders(){
    fetch(`new_orders.php?last=${lastCheck}`).then(res=>res.json()).then(data=>{
        if(data.length>0){data.forEach(order=>{showToast(`New Order: ${order.customer_name} ordered ${order.quantity} x ${order.item_name}`);}); lastCheck=new Date().toISOString().slice(0,19);}
    }).catch(err=>console.error(err));
}
function showToast(message){const container=document.getElementById('toast-container'); const toast=document.createElement('div'); toast.className='toast'; toast.textContent=message; container.appendChild(toast); setTimeout(()=>container.removeChild(toast),5000);}
setInterval(checkNewOrders,5000);

// Low Stock Alerts
function loadLowStockAlerts(){fetch('inventory_data.php').then(res=>res.json()).then(data=>{const ul=document.getElementById('low-stock-list'); ul.innerHTML=''; data.forEach(item=>{ul.innerHTML+=`<li>⚠️ ${item.item_name} - ${item.stock_quantity} ${item.unit} left</li>`;});});}
loadLowStockAlerts();
setInterval(loadLowStockAlerts,10000);

// Load Sales Data (Daily Revenue + Payments)
function loadSalesData(){
    fetch('sales_data.php').then(res=>res.json()).then(data=>{
        document.getElementById('daily-revenue').textContent=data.dailyRevenue.toFixed(2);
        const tbody=document.getElementById('payments-body'); tbody.innerHTML='';
        data.payments.forEach(p=>{tbody.innerHTML+=`<tr style="text-align:center;"><td>${p.id}</td><td>${p.customer_name}</td><td>${p.item_name}</td><td>${p.quantity}</td><td>${p.amount}</td><td>${p.payment_method}</td><td>${p.payment_time}</td></tr>`;});
    }).catch(err=>console.error(err));
}
loadSalesData(); setInterval(loadSalesData,10000);

// Mobile menu toggle
const menuToggle=document.querySelector('.menu-toggle');
const navbarCenter=document.querySelector('.navbar-center');
menuToggle.addEventListener('click',()=>{navbarCenter.classList.toggle('active');});
</script>
</body>
</html>
