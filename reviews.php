<?php
// reviews.php — VRS-60: Reviews & Ratings Page
include 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'includes/csrf.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? (int) $_SESSION['user_id'] : 0;
$csrfToken = generateCSRF();

// Check if logged-in user has a completed booking (to allow review submission)
$canReview = false;
if ($isLoggedIn) {
    $chk = $pdo->prepare("SELECT id FROM bookings WHERE user_id = ? AND status = 'completed' LIMIT 1");
    $chk->execute([$userId]);
    $canReview = (bool) $chk->fetch();
}

// Fetch user's own reviews
$myReviews = [];
if ($isLoggedIn) {
    $myStmt = $pdo->prepare("SELECT * FROM site_reviews WHERE user_id = ? ORDER BY created_at DESC");
    $myStmt->execute([$userId]);
    $myReviews = $myStmt->fetchAll();
}

// Rating filter
$ratingFilter = intval($_GET['rating'] ?? 0);
$where = "WHERE sr.status = 'approved'";
$params = [];
if ($ratingFilter >= 1 && $ratingFilter <= 5) {
    $where .= " AND sr.rating = ?";
    $params[] = $ratingFilter;
}

// Fetch approved public reviews
$revStmt = $pdo->prepare("
    SELECT sr.*, u.name AS reviewer_name
    FROM site_reviews sr
    JOIN users u ON sr.user_id = u.id
    $where
    ORDER BY sr.created_at DESC
    LIMIT 50
");
$revStmt->execute($params);
$reviews = $revStmt->fetchAll();

// Average rating
$avgStmt = $pdo->query("SELECT AVG(rating) as avg, COUNT(*) as total FROM site_reviews WHERE status='approved'");
$avgData = $avgStmt->fetch();
$avgRating = $avgData['avg'] ? round($avgData['avg'], 1) : 0;
$totalReviews = $avgData['total'] ?? 0;

// Rating distribution
$distStmt = $pdo->query("SELECT rating, COUNT(*) as cnt FROM site_reviews WHERE status='approved' GROUP BY rating ORDER BY rating DESC");
$distribution = $distStmt->fetchAll();
$distMap = [];
foreach ($distribution as $d) {
    $distMap[$d['rating']] = $d['cnt'];
}
?>
<?php include 'includes/footer.php'; ?>