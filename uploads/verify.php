<?php include 'includes/header.php'; ?>
<?php 
// Only allow access if temporary session is set
if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['verification_code'])) {
    header("Location: login.php");
    exit;
}
?>
<?php include 'includes/footer.php'; ?>
