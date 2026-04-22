<?php
// api/auth.php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'register') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($name) || empty($email) || empty($password) || empty($phone)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'Email already registered.']);
                exit;
            }

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, 'customer')");
            $stmt->execute([$name, $email, $hashed, $phone]);

            echo json_encode(['success' => true, 'message' => 'Registration successful!']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
        }
    } 
    elseif ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR name = ?");
            $stmt->execute([$email, $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Set Session Variables directly (No 2FA required)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                // Determine redirect path
                $redirect = ($user['role'] === 'admin') ? 'admin_dashboard.php' : 'index.php';
                
                echo json_encode(['success' => true, 'redirect' => $redirect, 'message' => 'Login successful!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Login error.']);
        }
    }
    elseif ($action === 'verify_code') {
        $code = trim($_POST['code'] ?? '');
        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Please enter the verification code.']);
            exit;
        }

        if (isset($_SESSION['verification_code']) && $_SESSION['verification_code'] == $code) {
            // Apply session data
            $_SESSION['user_id'] = $_SESSION['temp_user_id'];
            $_SESSION['name'] = $_SESSION['temp_user_name'];
            $_SESSION['role'] = $_SESSION['temp_user_role'];
            
            // Clean up temporary variables
            unset($_SESSION['temp_user_id'], $_SESSION['temp_user_name'], $_SESSION['temp_user_role'], $_SESSION['verification_code']);
            
            // Redirect customer to home page index.php
            echo json_encode(['success' => true, 'redirect' => 'index.php', 'message' => 'Verification successful!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid verification code. Please try again.']);
        }
    }
    elseif ($action === 'forgot_password') {
        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Please enter your email or username.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR name = ?");
            $stmt->execute([$email, $email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate 6-digit reset code
                $code = rand(100000, 999999);
                $_SESSION['reset_email'] = $user['email'];
                $_SESSION['reset_code'] = $code;
                
                // Simulation: Display code for dev convenience
                echo json_encode(['success' => true, 'message' => "Reset code: $code (Simulated email sent to " . $user['email'] . ")", 'redirect' => 'reset_password.php']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No account found with that email or username.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error.']);
        }
    }
    elseif ($action === 'reset_password') {
        $code = trim($_POST['code'] ?? '');
        $newPassword = $_POST['password'] ?? '';
        
        if (empty($code) || empty($newPassword)) {
            echo json_encode(['success' => false, 'message' => 'Code and new password are required.']);
            exit;
        }

        if (isset($_SESSION['reset_code']) && $_SESSION['reset_code'] == $code) {
            try {
                $email = $_SESSION['reset_email'];
                $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
                $stmt->execute([$hashed, $email]);

                unset($_SESSION['reset_code'], $_SESSION['reset_email']);
                echo json_encode(['success' => true, 'message' => 'Password reset successful! You can now log in.', 'redirect' => 'login.php']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Failed to reset password.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired reset code.']);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'logout') {
    session_destroy();
    header("Location: ../login.php");
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
