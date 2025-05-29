<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_login();

if (!is_admin()) {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$doc = get_document($id);

if (!$doc) {
    $_SESSION['error'] = "Document not found.";
    header("Location: index.php");
    exit;
}

$categories = [
    'manuals' => 'Manuals',
    'procedures' => 'Procedures',
    'work_instructions' => 'Work Instructions',
    'policies' => 'Policies',
    'others' => 'Others'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Invalid CSRF token.";
        header("Location: edit.php?id={$id}");
        exit;
    }

    // Retrieve form fields
    $document_no = trim($_POST['document_no'] ?? '');
    $revision = trim($_POST['revision'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $retention = trim($_POST['retention'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $pic = trim($_POST['pic'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');
    $establish_date = trim($_POST['establish_date'] ?? ''); // <-- Added

    // Validate
    if (empty($document_no) || empty($revision) || empty($title) || empty($category) || empty($retention) || empty($department) || empty($pic) || empty($establish_date)) {
        $_SESSION['error'] = "Please fill in all required fields.";
    } else {
        $mysqli = db_connect();
        $stmt = $mysqli->prepare("UPDATE documents SET document_no=?, revision=?, title=?, category=?, retention=?, department=?, pic=?, remarks=?, establish_date=? WHERE id=?");
        $stmt->bind_param("sssssssssi", $document_no, $revision, $title, $category, $retention, $department, $pic, $remarks, $establish_date, $id);
        if ($stmt->execute()) {
            log_action($_SESSION['user']['id'], "Edited document: $title", $id);
            $_SESSION['message'] = "Document updated successfully.";
            header("Location: view.php?id=" . $id);
            exit;
        } else {
            $_SESSION['error'] = "Failed to update document.";
        }
    }
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include_once __DIR__ . '/../templates/sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <h4>Edit Document</h4>
            <?php include_once __DIR__ . '/../templates/messages.php'; ?>

            <form method="post" class="mt-3">
                <input type="hidden" name="csrf_token" value="<?=csrf_token();?>">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label>Document No *</label>
                        <input type="text" name="document_no" class="form-control" readonly required value="<?=htmlspecialchars($doc['document_no'])?>">
                    </div>
                    <div class="col-md-1">
                        <label>Revision *</label>
                        <input type="text" name="revision" class="form-control" required value="<?=htmlspecialchars($doc['revision'])?>">
                    </div>
                    <div class="col-md-5">
                        <label>Document Name *</label>
                        <input type="text" name="title" class="form-control" required value="<?=htmlspecialchars($doc['title'])?>">
                    </div>
                    <div class="col-md-2">
                        <label>Effective Date *</label>
                        <input type="date" name="establish_date" class="form-control" required value="<?=htmlspecialchars($doc['establish_date'])?>">
                    </div>
                    <div class="col-md-2">
                        <label>Retention (Years) *</label>
                        <input type="number" name="retention" class="form-control" min="1" max="99" required value="<?=htmlspecialchars($doc['retention'])?>">
                    </div>
					
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Department *</label>
                        <input type="text" name="department" class="form-control" required value="<?=htmlspecialchars($doc['department'])?>">
                    </div>
                    <div class="col-md-3">
                        <label>Prepared by *</label>
                        <input type="text" name="pic" class="form-control" required value="<?=htmlspecialchars($doc['pic'])?>">
                    </div>
                    <div class="col-md-3">
                        <label>Category *</label>
                        <select name="category" class="form-control" required>
                            <?php foreach($categories as $val=>$label): ?>
                                <option value="<?=$val;?>" <?=($doc['category'] === $val ? 'selected' : '')?>><?=$label;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

					<div class="col-md-3">
						<label for="file">File (PDF, DOC, DOCX) *</label>
						<?php if (!empty($doc['id']) && !empty($doc['filename'])): ?>
							<div class="mb-1">

							</div>
						<?php endif; ?>
						<input type="file" id="file" name="file" class="form-control" accept=".pdf,.doc,.docx">
						<a href="/dms/public/view.php?id=<?=urlencode($doc['id'])?>&download=1" target="_blank" class="btn btn-link p-0" title="View or download last uploaded file">
						<i class="bi bi-file-earmark-arrow-down"></i>View Last Uploaded (<?=htmlspecialchars(pathinfo($doc['filename'], PATHINFO_EXTENSION))?>)</a>
					</div>

                </div>
				<div class="row mb-3">
					<div class="col-md-12">
						<label for="remarks" class="form-label">New Changes</label>
						<textarea name="remarks" id="remarks" class="form-control" rows="14"><?= htmlspecialchars($doc['remarks']) ?></textarea>
					</div>
				</div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="view.php?id=<?=$doc['id']?>" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>
