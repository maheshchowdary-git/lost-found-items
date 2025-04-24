

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
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$conn = new mysqli("localhost", "root", "", "lost_and_found");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT i.item_id, i.item_name, i.item_type, i.description, i.date_found, 
               i.found_by, i.contact_info, i.status, i.claimed_by, 
               c.name AS category_name, l.name AS location_name, 
               sl.old_status, sl.new_status, sl.changed_at
        FROM items i
        JOIN categories c ON i.category_id = c.category_id
        JOIN locations l ON i.location_id = l.location_id
        LEFT JOIN (
            SELECT s1.item_id, s1.old_status, s1.new_status, s1.changed_at
            FROM status_logs s1
            INNER JOIN (
                SELECT item_id, MAX(changed_at) AS latest_change
                FROM status_logs
                GROUP BY item_id
            ) s2 ON s1.item_id = s2.item_id AND s1.changed_at = s2.latest_change
        ) sl ON i.item_id = sl.item_id
        ORDER BY i.date_found DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Lost & Found Items</title>
  <style>
body {
  font-family: 'Arial', sans-serif;
  background-color: #f7f7f7;
  color: #333;
  margin: 0;
  padding: 20px;
}

h1 {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 40px;
  font-size: 2rem;
}

.item {
  background-color: #fff;
  border-radius: 10px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.item:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 10px rgba(0, 0, 0, 0.15);
}

.item h3 {
  font-size: 1.5rem;
  color: #333;
  margin: 0 0 10px;
}

.item p {
  font-size: 1rem;
  margin: 8px 0;
  color: #555;
}

.badge {
  padding: 5px 15px;
  border-radius: 20px;
  font-size: 0.875rem;
  color: #fff;
  display: inline-block;
  margin-bottom: 15px;
}

.lost {
  background-color: #e74c3c;
}

.found {
  background-color: #2ecc71;
}

button {
  background-color: #2ecc71;
  color: white;
  padding: 10px 20px;
  border-radius: 5px;
  border: none;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  margin-top: 15px;
  width: 100%;
}

button:hover {
  background-color: #27ae60;
}

button:disabled {
  background-color: #95a5a6;
  cursor: not-allowed;
}

form {
  text-align: center;
  margin-top: 20px;
}

.claimed-info {
  background-color: #f4f4f4;
  padding: 15px;
  margin-top: 20px;
  border-radius: 8px;
  font-style: italic;
  font-size: 1rem;
}

.claimed-info p {
  margin: 5px 0;
}

img {
  max-width: 100%;
  border-radius: 8px;
  margin-top: 15px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

a {
  display: block;
  text-align: center;
  margin-top: 40px;
  padding: 10px 20px;
  background-color: #3498db;
  color: white;
  text-decoration: none;
  border-radius: 5px;
  width: 250px;
  margin: 0 auto;
  margin-bottom: 20px;
}

a:hover {
  background-color: #2980b9;
}
  </style>
</head>
<body>

<h1>📋 Lost & Found Items</h1>

<a href="index.html" style="text-decoration:none; background:#3498db; color:white; padding:8px 15px; border-radius:5px;">➕ Submit a New Item</a>

<?php if ($result->num_rows > 0): ?>
  <?php while($row = $result->fetch_assoc()): ?>
    <div class="item">
      <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
      <span class="badge <?php echo $row['item_type'] === 'lost' ? 'lost' : 'found'; ?>">
        <?php echo ucfirst($row['item_type']); ?>
      </span>
      <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
      <p><strong>Found Date:</strong> <?php echo htmlspecialchars($row['date_found']); ?></p>
      <p><strong>Reported By:</strong> <?php echo htmlspecialchars($row['found_by']); ?></p>
      <p><strong>Contact:</strong> <?php echo htmlspecialchars($row['contact_info']); ?></p>
      <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category_name']); ?></p>
      <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location_name']); ?></p>
      
      <?php
      // Fetch images for this item
      $item_id = $row['item_id'];
      $image_sql = "SELECT image_path FROM images WHERE item_id = ?";
      $stmt = $conn->prepare($image_sql);
      $stmt->bind_param("i", $item_id);
      $stmt->execute();
      $image_result = $stmt->get_result();
      
      // Display the most recent status change
      $status = $row['status']; // Default to current item status
      if (!empty($row['new_status']) && !empty($row['old_status'])) {
          $status = $row['new_status'] . " (Changed from: " . $row['old_status'] . ")";
      }
      ?>
      <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>
      <p><strong>Last Status Change:</strong> <?php echo htmlspecialchars($row['changed_at'] ?? 'Not available'); ?></p>

      <?php
      // Check and display images
      if ($image_result->num_rows > 0): 
        while ($image_row = $image_result->fetch_assoc()):
?>
          <p><img src="<?php echo htmlspecialchars($image_row['image_path']); ?>" alt="Item Image" width=30%></p>
<?php 
        endwhile; 
      endif;
      ?>

      <?php
      echo "<!-- DEBUG: item_type = {$row['item_type']}, claimed_by = {$row['claimed_by']}, user_id = " . ($_SESSION['user_id'] ?? 'none') . " -->";

      $claimed_by_empty = empty($row['claimed_by']) || $row['claimed_by'] == 0;
      $can_claim = isset($_SESSION['user_id']) &&
                   in_array($row['item_type'], ['found']) &&
                   $claimed_by_empty;
      echo "<!-- DEBUG: can_claim = " . ($can_claim ? "true" : "false") . ", claimed_by = {$row['claimed_by']} -->";
      if ($can_claim):
      ?>
        <form method="POST" action="claim.php" style="margin-top: 10px;">
            <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
            <button type="submit" style="padding: 5px 10px; background-color: #2ecc71; color: white; border: none; border-radius: 5px; cursor: pointer;">
                ✅ Claim This Item
            </button>
        </form>
      <?php else: ?>
        <?php if (!empty($row['claimed_by'])): 
            // Fetch username from users table based on claimed_by user_id
            $claimed_by_id = $row['claimed_by'];
            $user_stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
            $user_stmt->bind_param("i", $claimed_by_id);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            $username = "Unknown User";
            if ($user_result->num_rows > 0) {
                $user_row = $user_result->fetch_assoc();
                $username = $user_row['username'];
            }
        ?>
          <p><strong>Claimed By:</strong> <?php echo htmlspecialchars($username); ?></p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p>No items found.</p>
<?php endif; ?>



</body>
</html>

<?php $conn->close(); ?>
