<?php 
include 'includes/header.php'; 
require_once 'includes/db_connect.php';

try {
    $stmtVehicles = $pdo->query("SELECT * FROM vehicles WHERE status = 'available' ORDER BY created_at DESC LIMIT 3");
    $featuredVehicles = $stmtVehicles->fetchAll();
} catch (PDOException $e) {
    $featuredVehicles = [];
}
?>
<?php include 'includes/footer.php'; ?>