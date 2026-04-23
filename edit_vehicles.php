<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once 'includes/db_connect.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$id]);
$v = $stmt->fetch();

if (!$v) {
    echo "<div class='container' style='padding: 4rem 0; text-align: center;'><h2>Vehicle not found.</h2><a href='manage_vehicles_ui.php' class='btn btn-outline'>Back to Management</a></div>";
    include 'includes/footer.php';
    exit;
}
?>
<?php include 'includes/footer.php'; ?>