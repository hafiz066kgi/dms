<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

$category = $_GET['category'] ?? null;
$search   = trim($_GET['search'] ?? '');
$documents = get_documents($category);
$user = current_user();

include_once __DIR__ . '/../templates/header.php';
?>
<style>
/* Custom style for masterlist - makes font smaller and harmonized */
.masterlist-small {
    font-size: 0.93rem;
}
.masterlist-small .form-control,
.masterlist-small .form-select,
.masterlist-small .btn,
.masterlist-small .table,
.masterlist-small .table th,
.masterlist-small .table td,
.masterlist-small .dropdown-item,
.masterlist-small .badge {
    font-size: 0.92em !important;
}
@media (max-width: 991.98px) {
    .container-fluid {
        padding-top: 1.5rem !important;
    }
}
</style>

<div class="container-fluid px-1 px-sm-2 px-md-3 masterlist-small">
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <h3 class="mb-0">Document Masterlist</h3>
                <?php if (is_admin()): ?>
                    <a href="upload.php" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                        <i class="bi bi-cloud-upload"></i> <span class="d-none d-sm-inline">Upload New Document</span>
                    </a>
                <?php endif; ?>
            </div>

            <?php include_once __DIR__ . '/../templates/messages.php'; ?>

            <form class="mb-3" method="get" action="index.php">
                <div class="row g-2">
                    <div class="col-12 col-md-5">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by document name, number, or department" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-7 col-md-4">
                        <select name="category" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            <option value="manuals" <?=($category == 'manuals' ? 'selected' : '')?>>Manuals</option>
                            <option value="procedures" <?=($category == 'procedures' ? 'selected' : '')?>>Procedures</option>
                            <option value="work_instructions" <?=($category == 'work_instructions' ? 'selected' : '')?>>Work Instructions</option>
                            <option value="policies" <?=($category == 'policies' ? 'selected' : '')?>>Policies</option>
                            <option value="others" <?=($category == 'others' ? 'selected' : '')?>>Others</option>
                        </select>
                    </div>
                    <div class="col-5 col-md-2">
                        <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-funnel"></i> <span class="d-none d-sm-inline">Filter</span></button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle shadow-sm bg-white small">
                    <thead class="table-primary sticky-top" style="z-index:1">
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Doc. No</th>
                            <th class="text-center">Rev.</th>
                            <th>Document Name</th>
                            <th class="text-center d-none d-md-table-cell">Retention</th>
                            <th class="text-center">Effective Date</th>
                            <th class="text-center d-none d-sm-table-cell">Review</th>
                            <th class="text-center d-none d-sm-table-cell">Dept</th>
                            <th class="text-center d-none d-sm-table-cell">Category</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 1;
                        $found = false;
                        if (!empty($documents)):
                            foreach ($documents as $doc):
                                // Inline search filter
                                if ($search !== '') {
                                    $term = strtolower($search);
                                    if (
                                        strpos(strtolower($doc['title']), $term) === false &&
                                        strpos(strtolower($doc['document_no']), $term) === false &&
                                        strpos(strtolower($doc['department']), $term) === false
                                    ) {
                                        continue;
                                    }
                                }
                                $found = true;
                                $establish_date = $doc['establish_date'] ?? null;
                                $retention = (int)($doc['retention'] ?? 0);
                                $date_to_review = '-';
                                if ($establish_date && $retention > 0) {
                                    try {
                                        $review_date = new DateTime($establish_date);
                                        $review_date->modify("+$retention years");
                                        $date_to_review = $review_date->format('d-m-Y');
                                    } catch (Exception $e) {
                                        $date_to_review = '-';
                                    }
                                }
                        ?>
                        <tr>
                            <td class="text-center"><?= $count++ ?></td>
                            <td class="text-center"><?= htmlspecialchars($doc['document_no']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($doc['revision']) ?></td>
                            <td><?= htmlspecialchars($doc['title']) ?></td>
                            <td class="text-center d-none d-md-table-cell"><?= htmlspecialchars($doc['retention']) ?></td>
                            <td class="text-center"><?= $establish_date ? htmlspecialchars(date('d-m-Y', strtotime($establish_date))) : '-' ?></td>
                            <td class="text-center d-none d-sm-table-cell"><?= htmlspecialchars($date_to_review) ?></td>
                            <td class="text-center d-none d-sm-table-cell"><?= htmlspecialchars($doc['department']) ?></td>
                            <td class="text-center d-none d-sm-table-cell">
                                <?php
                                    if ($doc['category'] === 'manuals') {
                                        echo '<span class="badge bg-success">Manuals</span>';
                                    } elseif ($doc['category'] === 'procedures') {
                                        echo '<span class="badge bg-primary">Procedures</span>';
                                    } elseif ($doc['category'] === 'work_instructions') {
                                        echo '<span class="badge bg-warning text-dark">Work Instructions</span>';
                                    } elseif ($doc['category'] === 'policies') {
                                        echo '<span class="badge bg-info text-dark">Policies</span>';
                                    } elseif ($doc['category'] === 'others') {
                                        echo '<span class="badge bg-secondary">Others</span>';
                                    }
                                ?>
                            </td>
                            <td class="text-center">
                                <!-- On mobile, group actions in dropdown -->
                                <div class="d-sm-none dropdown">
                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="view.php?id=<?= $doc['id'] ?>" class="dropdown-item">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </li>
                                        <?php if (is_admin() || is_superadmin()): ?>
                                        <li>
                                            <a href="edit.php?id=<?= $doc['id'] ?>" class="dropdown-item">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a href="delete.php?id=<?= $doc['id'] ?>"
                                               class="dropdown-item text-danger"
                                               onclick="return confirm('Delete this document?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <!-- On desktop, show direct action buttons -->
                                <div class="d-none d-sm-inline-flex gap-1">
                                    <a href="view.php?id=<?= $doc['id'] ?>" class="btn btn-info btn-sm" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if (is_admin() || is_superadmin()): ?>
                                    <a href="edit.php?id=<?= $doc['id'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="delete.php?id=<?= $doc['id'] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete this document?')" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach;
                        endif;
                        if (!$found): ?>
                        <tr>
                            <td colspan="10" class="text-center">No documents found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                <a href="download_masterlist.php<?= isset($category) ? '?category=' . urlencode($category) : '' ?>" class="btn btn-success btn-sm mb-3 ms-2">
                    <i class="bi bi-file-earmark-excel"></i> Download Masterlist
                </a>
            </div>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>
