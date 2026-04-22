<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'includes/header.php';
require_once 'includes/db_connect.php';

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$role = $user['role'] ?? 'customer';
if ($role === 'admin') {
    $isFullyVerified = true; // Admins are inherently trusted
} else {
    // Check Verification Status for customers
    $stmtDocs = $pdo->prepare("SELECT COUNT(*) FROM user_documents WHERE user_id = ? AND status = 'verified'");
    $stmtDocs->execute([$userId]);
    $verifiedDocs = $stmtDocs->fetchColumn();
    $isFullyVerified = $verifiedDocs >= 2; // Assuming citizenship and license
}
?>
<?php include 'includes/footer.php'; ?>