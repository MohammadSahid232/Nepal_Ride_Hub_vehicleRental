<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once 'includes/db_connect.php';

// Fetch all bookings
$stmt = $pdo->query("
    SELECT b.*, u.name as user_name, u.email, v.name as vehicle_name, v.type as vehicle_type
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN vehicles v ON b.vehicle_id = v.id
    ORDER BY b.created_at DESC
");
$bookings = $stmt->fetchAll();
?>