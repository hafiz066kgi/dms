<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

if (!is_admin() && !is_superadmin()) {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: index.php");
    exit;
}

// Pagination settings
$rows_per_page = 10;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $rows_per_page;

// Total rows count
$mysqli = db_connect();
$total_result = $mysqli->query("SELECT COUNT(*) as cnt FROM audit_log");
$total_rows = ($total_result && $row = $total_result->fetch_assoc()) ? intval($row['cnt']) : 0;
$total_pages = max(1, ceil($total_rows / $rows_per_page));

// Fetch logs for current page
$result = $mysqli->query("
    SELECT l.*, u.username 
    FROM audit_log l 
    LEFT JOIN users u ON l.user_id = u.id 
    ORDER BY l.timestamp DESC
    LIMIT $rows_per_page OFFSET $offset
");

include_once __DIR__ . '/../templates/header.php';
?>

<!-- Bootstrap Icons (required for stylish pagination) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
.log-table-container { font-size: 0.92rem; }
.log-search-input { max-width: 330px; font-size: 0.92rem; }
.log-table thead th, .log-table tbody td { vertical-align: middle; white-space: nowrap; }
@media (max-width: 576px) {
    .log-search-input { width: 100%; max-width: 100%; margin-bottom: 10px; }
}
.stylish-pagination .page-item { margin: 0 2px; }
.stylish-pagination .page-link {
    border-radius: 1.5rem !important;
    background: #f7f9fa;
    color: #222;
    border: none;
    box-shadow: 0 1px 3px rgba(80,80,80,0.07);
    padding: 0.5rem 1.15rem;
    min-width: 40px;
    min-height: 40px;
    font-size: 1.1rem;
    transition: background 0.18s, color 0.18s, box-shadow 0.18s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.stylish-pagination .page-link:focus,
.stylish-pagination .page-link:hover {
    background: #eef3fb;
    color: #0056b3;
    text-decoration: none;
    outline: none;
    box-shadow: 0 2px 8px rgba(100,130,200,0.13);
}
.stylish-pagination .page-item.active .page-link {
    background: linear-gradient(90deg, #3b82f6 30%, #60a5fa 100%);
    color: #fff !important;
    font-weight: 600;
    border: none;
    box-shadow: 0 4px 14px rgba(59,130,246,0.15);
}
.stylish-pagination .page-item.disabled .page-link {
    background: #f0f2f4;
    color: #bbb;
    pointer-events: none;
    box-shadow: none;
}
@media (max-width: 575px) {
    .stylish-pagination .page-link {
        min-width: 34px;
        min-height: 34px;
        font-size: 1rem;
        padding: 0.35rem 0.8rem;
    }
}
</style>

<div class="container mt-4 log-table-container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                <h4 class="mb-0">System Activity Log</h4>
                <input type="text" id="logSearch" class="form-control form-control-sm log-search-input" placeholder="Search user, action, document...">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm log-table" id="activityLogTable">
                    <thead class="table-dark">
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Document ID</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php $row_num = 0; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                    $row_num++;
                                    $is_latest = $row_num === 1 && $page === 1;
                                    $username = htmlspecialchars($row['username'] ?? $row['user_id']);
                                    $action = htmlspecialchars($row['action']);
                                    $docid = htmlspecialchars($row['document_id']);
                                    $datetime = htmlspecialchars(
                                        date('d-m-Y, h:i A', strtotime($row['timestamp']))
                                    );
                                ?>
                                <tr<?= $is_latest ? ' style="background-color:#e7f4fd;" title="Most recent"' : '' ?>>
                                    <td><?= $username ?></td>
                                    <td>
                                        <?php if (strlen($action) > 50): ?>
                                            <span title="<?= $action ?>"><?= substr($action,0,50) . '…' ?></span>
                                        <?php else: ?>
                                            <?= $action ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $docid ?></td>
                                    <td><?= $datetime ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No log entries found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Stylish Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Activity Log Pagination">
                <ul class="pagination justify-content-center stylish-pagination flex-wrap">
                    <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                        <a class="page-link" href="?page=1" tabindex="-1" aria-label="First"><i class="bi bi-chevron-bar-left"></i></a>
                    </li>
                    <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page-1 ?>" aria-label="Previous"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <?php
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        for ($i = $start; $i <= $end; $i++):
                    ?>
                        <li class="page-item<?= $i == $page ? ' active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item<?= $page >= $total_pages ? ' disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page+1 ?>" aria-label="Next"><i class="bi bi-chevron-right"></i></a>
                    </li>
                    <li class="page-item<?= $page >= $total_pages ? ' disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $total_pages ?>" aria-label="Last"><i class="bi bi-chevron-bar-right"></i></a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

            <a href="index.php" class="btn btn-secondary btn-sm mt-2">Back</a>
        </div>
    </div>
</div>

<script>
// Live search for current page
document.getElementById('logSearch').addEventListener('keyup', function() {
    let input = this.value.toLowerCase();
    let rows = document.querySelectorAll('#activityLogTable tbody tr');
    rows.forEach(row => {
        let txt = row.textContent.toLowerCase();
        row.style.display = txt.indexOf(input) > -1 ? '' : 'none';
    });
});

// Maintain and smoothly restore scroll position on pagination click
document.querySelectorAll('.stylish-pagination .page-link').forEach(function(link) {
    link.addEventListener('click', function(e) {
        if (!link.closest('.disabled')) {
            sessionStorage.setItem('scrollTop', window.scrollY);
        }
    });
});
window.addEventListener('DOMContentLoaded', function() {
    const scroll = sessionStorage.getItem('scrollTop');
    if (scroll !== null) {
        window.scrollTo({ top: parseInt(scroll, 10), behavior: 'smooth' }); // <-- smooth scroll here
        sessionStorage.removeItem('scrollTop');
    }
});
</script>


<?php include_once __DIR__ . '/../templates/footer.php'; ?>
