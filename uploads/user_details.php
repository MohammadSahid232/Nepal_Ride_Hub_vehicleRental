<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'includes/db_connect.php';

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Check Verification Status
$stmtDocs = $pdo->prepare("SELECT COUNT(*) FROM user_documents WHERE user_id = ? AND status = 'verified'");
$stmtDocs->execute([$userId]);
$verifiedDocs = $stmtDocs->fetchColumn();
$isFullyVerified = $verifiedDocs >= 2; // Assuming citizenship and license
?>