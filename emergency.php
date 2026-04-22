<?php
include 'includes/header.php';
require_once 'includes/db_connect.php';

// Fetch active emergency contacts
$stmt = $pdo->query("SELECT * FROM emergency_contacts WHERE is_active = 1 ORDER BY display_order ASC");
$contacts = $stmt->fetchAll();

$isLoggedIn = isset($_SESSION['user_id']);
$userId    = $isLoggedIn ? $_SESSION['user_id']   : null;
$userRole  = $isLoggedIn ? ($_SESSION['role'] ?? 'customer') : null;
$isAdmin   = ($userRole === 'admin');
$isCustomer = ($isLoggedIn && !$isAdmin);

// Only fetch bookings for customers
$bookings = [];
if ($isCustomer) {
    $bStmt = $pdo->prepare("SELECT b.id, v.name as vehicle_name, b.start_date, b.end_date FROM bookings b JOIN vehicles v ON b.vehicle_id = v.id WHERE b.user_id = ? AND b.status IN ('confirmed', 'pending') ORDER BY b.start_date DESC LIMIT 5");
    $bStmt->execute([$userId]);
    $bookings = $bStmt->fetchAll();
}
?>
<?php include 'includes/footer.php'; ?>