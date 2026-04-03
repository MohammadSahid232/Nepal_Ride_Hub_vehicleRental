<?php
// api/auth.php
header('Content-Type: application/json');
require_once '../db_connect.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'login') {
        $emailOrUsername = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (empty($emailOrUsername) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Please enter all fields.']);
            exit;
        }

        try {
            // Check by email or name
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR name = ?");
            $stmt->execute([$emailOrUsername, $emailOrUsername]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                $redirect = ($user['role'] === 'admin') ? 'admin_dashboard.php' : 'index.php';
                echo json_encode(['success' => true, 'message' => 'Login successful!', 'redirect' => $redirect]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } elseif ($action === 'register') {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '0000000000';

        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
            exit;
        }

        if ($password !== $password_confirm) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
            exit;
        }

        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Email already registered.']);
                exit;
            }

            // Hash password and insert
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, 'customer')");
            if ($stmt->execute([$name, $email, $password_hash, $phone])) {
                echo json_encode(['success' => true, 'message' => 'Registration successful! You can now log in.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } elseif ($action === 'logout') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: ../login.php');
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'logout') {
    // Handle GET logout for the link in header.php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();
    header('Location: ../login.php');
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
?>
