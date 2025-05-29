<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

$id = intval($_GET['id'] ?? 0);
$doc = get_document($id);

if (!$doc) {
    $_SESSION['error'] = "Document not found.";
    header("Location: index.php");
    exit;
}

// Secure file details
$category = $doc['category'];
$department = $doc['department'];
$filename = $doc['filename'];

// Sanitize department for file path (same as in upload)
$safeDepartment = preg_replace('/[^A-Za-z0-9_\- ]/', '', $department);
$safeDepartment = str_replace(' ', '_', $safeDepartment);
$file_path = __DIR__ . "/../uploads/{$category}/{$safeDepartment}/{$filename}";

// --- Download logic ---
if (isset($_GET['download'])) {
    if (file_exists($file_path)) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $downloadName = preg_replace('/[^A-Za-z0-9 _-]/', '', $doc['title']);
        $downloadName = str_replace(' ', '_', $downloadName) . '.' . $ext;
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        $_SESSION['error'] = "File not found on server.";
        header("Location: index.php");
        exit;
    }
}

// Date to Review Calculation
$establish_date = $doc['establish_date'] ?? null;
$retention = (int)($doc['retention'] ?? 0);
$date_to_review = '';
if ($establish_date && $retention > 0) {
    try {
        $review_date = new DateTime($establish_date);
        $review_date->modify("+$retention years");
        $date_to_review = $review_date->format('Y-m-d');
    } catch (Exception $e) {
        $date_to_review = '-';
    }
} else {
    $date_to_review = '-';
}

include_once __DIR__ . '/../templates/header.php';
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include_once __DIR__ . '/../templates/sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <h4 class="mb-4">View Document</h4>
            <div class="row">
                <!-- Main details table -->
                <div class="col-lg-5 col-md-12 mb-3">
                    <div class="table-responsive">
                        <table class="table table-bordered w-100 small mb-0">
                            <tr><th>Document No</th><td><?=htmlspecialchars($doc['document_no'])?></td></tr>
                            <tr><th>Revision</th><td><?=htmlspecialchars($doc['revision'])?></td></tr>
                            <tr><th>Document Name</th><td><?=htmlspecialchars($doc['title'])?></td></tr>
                            <tr><th>Retention (Years)</th><td><?=htmlspecialchars($doc['retention'])?></td></tr>
                            <tr>
							<th>Effective Date</th>
								<td>
									<?php
										if (!empty($establish_date) && $establish_date !== '0000-00-00') {
											echo htmlspecialchars(date('d-m-Y', strtotime($establish_date)));
										} else {
											echo '-';
										}
									?>
								</td>
							</tr>
							<tr>
								<th>Date to Review</th>
								<td>
									<?php
										if (!empty($date_to_review) && $date_to_review !== '0000-00-00') {
											echo htmlspecialchars(date('d-m-Y', strtotime($date_to_review)));
										} else {
											echo '-';
										}
									?>
								</td>
							</tr>
                            <tr><th>Department</th><td><?=htmlspecialchars($doc['department'])?></td></tr>
                            <tr><th>Prepared By</th><td><?=htmlspecialchars($doc['pic'])?></td></tr>
                            <tr><th>Category</th><td><?=htmlspecialchars(ucwords(str_replace('_', ' ', $doc['category'])))?></td></tr>
<tr>
    <th>Uploaded At</th>
    <td>
        <?php
            if (!empty($doc['uploaded_at']) && $doc['uploaded_at'] !== '0000-00-00 00:00:00') {
                echo htmlspecialchars(date('d-m-Y | h:i A', strtotime($doc['uploaded_at'])));
            } else {
                echo '-';
            }
        ?>
    </td>
</tr>


                            <tr><th>Uploaded By</th><td><?=htmlspecialchars($doc['uploaded_by'])?></td></tr>
                        </table>
                    </div>
                </div>
                <!-- Remarks table, shown beside -->
                <div class="col-lg-7 col-md-12 mb-3">
                    <div class="table-responsive">
                        <table class="table table-bordered w-100 small mb-0">
                            <tr>
                                <th style="width: 170px;">Remarks / Changes</th>
                                <td><?=htmlspecialchars($doc['remarks'])?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- Action Buttons below both tables, full width on mobile -->
                <div class="col-12 mt-3">
                    <a href="view.php?id=<?=$doc['id']?>&download=1" class="btn btn-success">Download File</a>
                    <?php if (is_admin()): ?>
                        <a href="edit.php?id=<?=$doc['id']?>" class="btn btn-warning ms-2">Edit</a>
                    <?php endif; ?>
					<a href="javascript:window.history.back();" class="btn btn-secondary ms-2">Back to Masterlist</a>
                </div>
            </div>
        </div>
        <div class="col-md-2"><!-- Optional: right margin --></div>
    </div>
</div>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>
