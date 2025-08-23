<?php
session_start();
// Database connection
$conn = mysqli_connect("localhost", "root", "1234", "canteen");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database and table if not exists
$sql = "CREATE DATABASE IF NOT EXISTS canteen_db";
mysqli_query($conn, $sql);
mysqli_select_db($conn, "canteen_db");
$sql = "CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    availability TINYINT(1) DEFAULT 1,
    category VARCHAR(50) NOT NULL
    

)";
mysqli_query($conn, $sql);

// Handle add item form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $availability = ($_POST['availability'] === "yes") ? 1 : 0;
    $sql = "INSERT INTO menu_items (name, description, price, availability, category) VALUES ('$name', '$description', '$price', '$availability', '$category')";
    mysqli_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f1f3f5;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #34495e;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .action-btn {
            padding: 8px 12px;
            margin-right: 5px;
            font-size: 14px;
        }
        .edit-btn {
            background-color: #ffc107;
        }
        .edit-btn:hover {
            background-color: #e0a800;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        th {
            background-color: #e9ecef;
            color: #495057;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
        }
        .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Menu Items</h2>

        <!-- Add Item Form -->
        <form method="POST" action="">
            <input type="hidden" name="add_item" value="1">
            <div class="form-group">
                <label for="name">Item Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="price">Price (Rs.)</label>
                <input type="number" step="0.01" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="Food">Food</option>
                    <option value="Drink">Drink</option>
                    <option value="ShortEats">ShortEats</option>
                    <option value="Dessert">Dessert </option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="availability">Category</label>
                <select id="availability" name="availability" required>
                    <option value="no">Not Available</option>
                    <option value="yes">Available</option>
                </select>
            
            </div>
            <button type="submit">Add</button>
        </form>

        <!-- Menu Items Table -->
        <h3>Menu Items</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="menuTable">
                <?php
                $result = mysqli_query($conn, "SELECT * FROM menu_items");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>Rs. " . number_format($row['price'], 2) . "</td>
                        <td>" . htmlspecialchars($row['category']) . "</td>
                        <td>" . htmlspecialchars($row['description']) . "</td>
                        <td>" . ($row['availability'] ? 'Yes' : 'No') . "</td>
                        <td>
                            <button class='action-btn edit-btn' onclick='openEditModal({$row['id']}, \"" . htmlspecialchars($row['name'], ENT_QUOTES) . "\", {$row['price']}, \"" . htmlspecialchars($row['category'], ENT_QUOTES) . "\", \"" . htmlspecialchars($row['description'], ENT_QUOTES) . "\",{$row['availability']})'>Edit</button>
                            <button class='action-btn delete-btn' onclick='deleteItem({$row['id']})'>Delete</button>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h3>Edit Item</h3>
            <form id="editForm">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="edit_name">Item Name</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="edit_price">Price (Rs.)</label>
                    <input type="number" step="0.01" id="edit_price" name="price" required>
                </div>
                <div class="form-group">
                    <label for="edit_category">Category</label>
                    <select id="edit_category" name="category" required>
                        <option value="Food">Food</option>
                        <option value="Drink">Drink</option>
                        <option value="ShortEats">ShortEats</option>
                        <option value="Dessert">Dessert</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_category">Availablity</label>
                <select id="edit_availability" name="availability" required>
                    <option value="no">Not Available</option>
                    <option value="yes">Available</option>
                </select>
                </div>
                <button type="button" onclick="updateItem()">Update Item</button>
            </form>
        </div>
    </div>

    <script>


        function openEditModal(id, name, price, category, description, availability) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_availability').value = availability ? "yes" : "no";
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function updateItem() {
    const form = document.getElementById('editForm');
    const formData = new FormData(form);

    // Convert FormData to plain object
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });

    fetch('update_item.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Item updated successfully!");
            location.reload();
        } else {
            alert('Error updating item: ' + data.error);
        }
    })
    .catch(error => alert('Error: ' + error));
}

    </script>
</body>
</html>