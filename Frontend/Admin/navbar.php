<!-- navbar.php -->
<style>
/* Sidebar Navigation Styles */
.sidebar {
    position: fixed;
    top: 0; left: 0;
    width: 250px;
    height: 100%;
    background: #1abc9c;
    color: #fff;
    display: flex;
    flex-direction: column;
    padding-top: 20px;
    transition: width 0.3s ease;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    z-index: 999;
}
.sidebar .brand {
    text-align: center;
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 30px;
    letter-spacing: 1px;
    color: #fff;
}
.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.sidebar ul li {
    width: 100%;
}
.sidebar ul li a {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: #fff;
    text-decoration: none;
    font-size: 1rem;
    transition: all 0.3s ease;
}
.sidebar ul li a:hover, 
.sidebar ul li a.active {
    background: rgba(255,255,255,0.2);
    border-left: 5px solid #f1c40f;
    padding-left: 25px;
}
.sidebar ul li a i {
    margin-right: 15px;
    font-size: 1.2rem;
}
.toggle-btn {
    position: absolute;
    top: 20px;
    right: -40px;
    background: #1abc9c;
    border-radius: 50%;
    padding: 10px;
    cursor: pointer;
    color: #fff;
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}
.sidebar.collapsed {
    width: 70px;
}
.sidebar.collapsed .brand,
.sidebar.collapsed ul li a span {
    display: none;
}
.sidebar.collapsed ul li a {
    justify-content: center;
}
.content {
    margin-left: 250px;
    padding: 20px;
    transition: margin-left 0.3s ease;
}
.sidebar.collapsed ~ .content {
    margin-left: 70px;
}
</style>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="sidebar" id="sidebar">
    <div class="brand">🍴 Canteen</div>
    <div class="toggle-btn" id="toggle-btn"><i class="fas fa-bars"></i></div>
    <ul>
        <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
        <li><a href="orders.php"><i class="fas fa-list"></i><span>Orders</span></a></li>
        <li><a href="inventory.php"><i class="fas fa-boxes"></i><span>Inventory</span></a></li>
        <li><a href="sales.php"><i class="fas fa-cash-register"></i><span>Payments</span></a></li>
        <li><a href="Menu-Management/menu_add.php"><i class="fas fa-utensils"></i><span>Menu</span></a></li>
        <li><a href="activity_logs.php"><i class="fas fa-history"></i><span>Activity Logs</span></a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
    </ul>
</div>

<div class="content">
    <!-- Your dashboard content starts here -->
</div>

<script>
document.getElementById("toggle-btn").addEventListener("click", function() {
    document.getElementById("sidebar").classList.toggle("collapsed");
});
</script>
