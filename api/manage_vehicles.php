<?php
session_start();
require_once '../includes/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'add_vehicle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = $_POST['type'] ?? '';
    $condition = $_POST['condition_type'] ?? 'city';
    $brand = trim($_POST['brand'] ?? '');
    $model_year = (int)($_POST['model_year'] ?? 0);
    $price = (float)($_POST['price_per_day'] ?? 0);
    $desc = trim($_POST['description'] ?? '');

    if(empty($name) || empty($type) || empty($price)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Valid vehicle image is required.']);
        exit;
    }

    $uploadDir = '../uploads/vehicles/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = $_FILES['image']['name'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if(!in_array($ext, ['jpg','jpeg','png','webp'])) {
        echo json_encode(['success' => false, 'message' => 'Image must be JPG, PNG, or WEBP.']);
        exit;
    }

    $newImgName = 'veh_' . time() . '.' . $ext;
    $dest = $uploadDir . $newImgName;
    $dbImgPath = 'uploads/vehicles/' . $newImgName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO vehicles (name, type, condition_type, brand, model_year, price_per_day, description, image_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'available')");
            $stmt->execute([$name, $type, $condition, $brand, $model_year, $price, $desc, $dbImgPath]);
            
            echo json_encode(['success' => true, 'message' => 'Vehicle added.']);
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'DB Error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
    }
}
elseif ($action === 'delete_vehicle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    try {
        // Find image path beforehand to delete file if needed
        $stmt = $pdo->prepare("SELECT image_path FROM vehicles WHERE id=?");
        $stmt->execute([$id]);
        $veh = $stmt->fetch();
        
        if($veh) {
            $path = '../' . $veh['image_path'];
            if(file_exists($path) && !is_dir($path)) unlink($path);
            
            $pdo->prepare("DELETE FROM vehicles WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Deleted']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Vehicle not found']);
        }
    } catch(PDOException $e) {
        // Will fail if bookings exist (unless ON DELETE CASCADE is set, which it is)
        echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Request']);
}
?>
