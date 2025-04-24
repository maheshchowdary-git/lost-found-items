<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_and_found";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Grab POST form data
$item_name     = $_POST['item_name'];
$description   = $_POST['description'];
$date_found    = $_POST['date_found'];
$category_id   = $_POST['category_id'];
$location_id   = $_POST['location_id'];
$found_by      = $_POST['found_by'];
$item_type     = $_POST['item_type'];
$contact_info  = $_POST['contact_info'];

// Removed unused SQL insert and binding that excludes image_path

// File upload handling
$image_path = null;
if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // create uploads folder if not exists
    }

    $tmp_name = $_FILES['item_image']['tmp_name'];
    $filename = basename($_FILES['item_image']['name']);
    $target_path = $upload_dir . time() . '_' . $filename;

    if (move_uploaded_file($tmp_name, $target_path)) {
        $image_path = $target_path;
    } else {
        die("❌ Failed to upload image.");
    }
}
// UPDATE your SQL to include image_path
$sql = "INSERT INTO items (
  item_name, description, date_found, category_id, location_id, found_by, item_type, contact_info
) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssissss", $item_name, $description, $date_found, $category_id, $location_id, $found_by, $item_type, $contact_info);

if ($stmt->execute()) {
  $new_item_id = $conn->insert_id;

  if ($image_path !== null) {
      $img_sql = "INSERT INTO images (item_id, image_path) VALUES (?, ?)";
      $img_stmt = $conn->prepare($img_sql);
      $img_stmt->bind_param("is", $new_item_id, $image_path);
      $img_stmt->execute();
      if ($img_stmt->affected_rows > 0) {
          echo "<pre>✅ Image inserted into DB for item ID $new_item_id: $image_path</pre>";
      } else {
          echo "<pre>❌ Image insert failed: " . $img_stmt->error . "</pre>";
      }
      $img_stmt->close();
  }
  
  echo "✅ Item submitted successfully!";
  header("Location: view_items.php"); // Redirect to the view items page
  exit();
} else {
  echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>