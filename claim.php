<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "lost_and_found");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$user_id = $_SESSION['user_id'];

// Fetch old status
$get_status = $conn->prepare("SELECT status FROM items WHERE item_id = ?");
$get_status->bind_param("i", $item_id);
$get_status->execute();
$res = $get_status->get_result();
$old_status = ($res->num_rows > 0) ? $res->fetch_assoc()['status'] : 'unknown';

// Update item to 'claimed'
$status = 'claimed';
$update_item = $conn->prepare("UPDATE items SET status = ?, claimed_by = ? WHERE item_id = ?");
$update_item->bind_param("sii", $status, $user_id, $item_id);
$update_item->execute();

// Insert into status_logs
$log = $conn->prepare("INSERT INTO status_logs (item_id, old_status, new_status, changed_at) VALUES (?, ?, 'claimed', NOW())");
$log->bind_param("is", $item_id, $old_status);
$log->execute();

header("Location: view_items.php");
exit;
?>