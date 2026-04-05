<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: uploads/login.php');
    exit;
}
require_once 'includes/db_connect.php';

// Stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn();
$totalVehicles = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$pendingBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
$pendingDocs = $pdo->query("SELECT COUNT(*) FROM user_documents WHERE status='pending'")->fetchColumn();
?>

<section style="padding: 4rem 0;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
            <h2>Admin Dashboard</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div style="background: var(--primary-blue); color: white; padding: 2rem; border-radius: 8px; text-align: center; box-shadow: var(--shadow);">
                <h3><?php echo $totalUsers; ?></h3>
                <p>Total Customers</p>
            </div>
            <div style="background: var(--primary-red); color: white; padding: 2rem; border-radius: 8px; text-align: center; box-shadow: var(--shadow);">
                <h3><?php echo $totalVehicles; ?></h3>
                <p>Total Vehicles</p>
            </div>
            <div style="background: #28a745; color: white; padding: 2rem; border-radius: 8px; text-align: center; box-shadow: var(--shadow);">
                <h3><?php echo $pendingBookings; ?></h3>
                <p>Pending Bookings</p>
            </div>
            <div style="background: #ffc107; color: #333; padding: 2rem; border-radius: 8px; text-align: center; box-shadow: var(--shadow);">
                <h3><?php echo $pendingDocs; ?></h3>
                <p>Docs to Verify</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Pending Documents -->
            <div style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3>Documents for Verification</h3>
                </div>
                <hr style="margin: 1.5rem 0;">
                <div id="docsTarget">
                    <p>Loading documents...</p>
                </div>
            </div>

            <!-- Quick Manage Links -->
            <div style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow);">
                <h3>Management Modules</h3>
                <hr style="margin: 1.5rem 0;">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <a href="manage_vehicles_ui.php" class="btn btn-outline btn-block" style="text-align: left;"><i class="fas fa-car"></i> Manage Vehicles</a>
                    <a href="manage_bookings_ui.php" class="btn btn-outline btn-block" style="text-align: left;"><i class="fas fa-calendar-alt"></i> Manage Bookings</a>
                    <a href="manage_users_ui.php" class="btn btn-outline btn-block" style="text-align: left;"><i class="fas fa-users"></i> Manage Users</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    // Load pending docs
    try {
        const response = await fetch('api/manage_users.php?action=list_pending_docs');
        const data = await response.json();
        const docsTarget = document.getElementById('docsTarget');
        
        if (data.success && data.documents.length > 0) {
            let html = '<ul style="list-style: none;">';
            data.documents.forEach(doc => {
                html += `
                    <li style="padding: 1rem; border: 1px solid var(--border-color); margin-bottom: 1rem; border-radius: 4px;">
                        <strong>User:</strong> ${doc.user_name} (${doc.email})<br>
                        <strong>Type:</strong> ${doc.document_type.toUpperCase()}<br>
                        <a href="${doc.file_path}" target="_blank" style="display: inline-block; margin: 0.5rem 0;">View Document</a>
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                            <button onclick="verifyDoc(${doc.id}, 'verified')" class="btn" style="background: #28a745; color: white; padding: 0.3rem 0.8rem;">Verify</button>
                            <button onclick="verifyDoc(${doc.id}, 'rejected')" class="btn" style="background: #dc3545; color: white; padding: 0.3rem 0.8rem;">Reject</button>
                        </div>
                    </li>
                `;
            });
            html += '</ul>';
            docsTarget.innerHTML = html;
        } else {
            docsTarget.innerHTML = '<p style="color: var(--success);"><i class="fas fa-check-circle"></i> No pending documents to verify!</p>';
        }
    } catch (e) {
        document.getElementById('docsTarget').innerHTML = 'Failed to load documents.';
    }
});

async function verifyDoc(docId, status) {
    if(!confirm(`Are you sure you want to mark this document as ${status}?`)) return;
    
    const formData = new FormData();
    formData.append('document_id', docId);
    formData.append('status', status);

    try {
        const response = await fetch('api/manage_users.php?action=verify_document', {
            method: 'POST', body: formData
        });
        const data = await response.json();
        if(data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch {
        alert('Request failed');
    }
}
</script>

<?php include 'includes/footer.php'; ?>
