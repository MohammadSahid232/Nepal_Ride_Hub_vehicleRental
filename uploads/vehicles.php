<?php
include 'includes/header.php';
require_once 'includes/db_connect.php';

$typeFilter = $_GET['type'] ?? '';
$locFilter = $_GET['location'] ?? ''; // Search matching name or brand currently since location is not in vehicles directly (gps simulated)
$condFilter = $_GET['condition'] ?? '';
$params = [];

$query = "SELECT * FROM vehicles WHERE status = 'available'";

if (!empty($typeFilter)) {
    $query .= " AND type = ?";
    $params[] = $typeFilter;
}

if (!empty($condFilter)) {
    $query .= " AND condition_type = ?";
    $params[] = $condFilter;
}

if (!empty($locFilter)) {
    $query .= " AND (name LIKE ? OR brand LIKE ?)";
    $params[] = "%$locFilter%";
    $params[] = "%$locFilter%";
}

$query .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $vehicles = $stmt->fetchAll();
} catch (PDOException $e) {
    $vehicles = [];
}
?>