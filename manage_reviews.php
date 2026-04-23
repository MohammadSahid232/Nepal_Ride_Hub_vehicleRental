<?php
// manage_reviews.php — Admin: Review Moderation Panel
include 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'includes/csrf.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

$csrfToken    = generateCSRF();
$filterStatus = $_GET['status'] ?? 'pending';

// Stats
$counts = [];
foreach (['pending', 'approved', 'rejected'] as $s) {
    $c = $pdo->prepare("SELECT COUNT(*) FROM site_reviews WHERE status = ?");
    $c->execute([$s]);
    $counts[$s] = $c->fetchColumn();
}
$counts['all'] = array_sum($counts);
?>
<?php include 'includes/footer.php'; ?>