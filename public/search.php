<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

$search = trim($_GET['q'] ?? '');
$documents = [];

if ($search !== '') {
    $mysqli = db_connect();
    $q = "%$search%";
    $stmt = $mysqli->prepare("SELECT d.*, u.username as uploaded_by FROM documents d LEFT JOIN users u ON d.uploaded_by=u.id WHERE d.document_no LIKE ? OR d.title LIKE ? OR d.department LIKE ? OR d.pic LIKE ?");
    $stmt->bind_param('ssss', $q, $q, $q, $q);
    $stmt->execute();
    $documents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include_once __DIR__ . '/../templates/sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <h4>Search Results for "<?=htmlspecialchars($search)?>"</h4>
            <form class="mb-3" method="get" action="search.php">
                <input type="text" name="q" class="form-control" value="<?=htmlspecialchars($search)?>" placeholder="Search...">
            </form>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Document No</th>
                            <th>Revision</th>
                            <th>Document Name</th>
                            <th>Department</th>
                            <th>PIC</th>
                            <th>Category</th>
                            <th>Date Uploaded</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($documents)): $count=1; foreach($documents as $doc): ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= htmlspecialchars($doc['document_no']) ?></td>
                            <td><?= htmlspecialchars($doc['revision']) ?></td>
                            <td><?= htmlspecialchars($doc['title']) ?></td>
                            <td><?= htmlspecialchars($doc['department']) ?></td>
                            <td><?= htmlspecialchars($doc['pic']) ?></td>
                            <td><?= htmlspecialchars(ucwords(str_replace('_',' ',$doc['category']))) ?></td>
                            <td><?= htmlspecialchars($doc['uploaded_at']) ?></td>
                            <td>
                                <a href="view.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="9" class="text-center">No results found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>
