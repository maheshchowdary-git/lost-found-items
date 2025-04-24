<?php
session_start();
if (isset($_SESSION['user_id'])) {
    echo '<div style="position:absolute; top:20px; right:20px;">
            <form action="logout.php" method="POST" style="display:inline;">
              <button type="submit" style="background:#e74c3c; color:white; padding:8px 16px; border:none; border-radius:5px; font-weight:bold; cursor:pointer;">
                Logout
              </button>
            </form>
          </div>';
  }
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Only allow admin users
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "lost_and_found");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete action
if (isset($_GET['delete'])) {
    $item_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM images WHERE item_id = $item_id");
    $conn->query("DELETE FROM status_logs WHERE item_id = $item_id");
    $conn->query("DELETE FROM items WHERE item_id = $item_id");
    header("Location: admin.php");
    exit;
}

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status'], $_POST['item_id'])) {
    $item_id = (int)$_POST['item_id'];
    $new_status = $_POST['status'];

    $old_status_query = $conn->query("SELECT item_type FROM items WHERE item_id = $item_id");
    $old_status = $old_status_query->fetch_assoc()['item_type'];

    $stmt = $conn->prepare("INSERT INTO status_logs (item_id, old_status, new_status, changed_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $item_id, $old_status, $new_status);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE items SET item_type = ? WHERE item_id = ?");
    $stmt->bind_param("si", $new_status, $item_id);
    $stmt->execute();

    header("Location: admin.php");
    exit;
}

// Fetch items
$result = $conn->query("SELECT * FROM items ORDER BY date_found DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #eee; }
        form { display: inline; }
        .btn { padding: 5px 10px; text-decoration: none; border: none; cursor: pointer; border-radius: 5px; }
        .delete { background: #e74c3c; color: white; }
        .update { background: #3498db; color: white; }
    </style>
</head>
<body>

<h2>🛠️ Admin Dashboard</h2>
<p><a href="view_items.php">← Back to View Items</a></p>

<table>
    <tr>
        <th>Item</th>
        <th>Description</th>
        <th>Type</th>
        <th>Date</th>
        <th>Reported By</th>
        <th>Contact</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['item_type']); ?></td>
            <td><?php echo htmlspecialchars($row['date_found']); ?></td>
            <td><?php echo htmlspecialchars($row['found_by']); ?></td>
            <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
            <td>
                <form method="POST" style="margin-bottom:5px;">
                    <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                    <select name="status">
                        <option value="lost">Lost</option>
                        <option value="found">Found</option>
                        <option value="claimed">Claimed</option>
                    </select>
                    <button class="btn update" type="submit">Change</button>
                </form>
                <a class="btn delete" href="admin.php?delete=<?php echo $row['item_id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>