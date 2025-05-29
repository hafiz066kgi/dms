<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_login();

$user = current_user();

if (!is_admin()) {
    $_SESSION['error'] = "Unauthorized access.";
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

$departments = [
    'QEHS' => 'QEHS',
    'ENG' => 'ENG',
    'INJ' => 'INJ',
    'TLG' => 'TLG',
    'VAS' => 'VAS',
    'SCM' => 'SCM',
    'MNT' => 'MNT',	
    'BDS' => 'BDS',	
    'PPC' => 'PPC',		
    'HR' => 'HR',	
    'MIS' => 'MIS',
    'FIN' => 'FIN',
    'ALL' => 'All Department'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Invalid CSRF token.";
        header("Location: upload.php");
        exit;
    }

    // Retrieve form fields
    $document_no    = trim($_POST['document_no'] ?? '');
    $revision       = trim($_POST['revision'] ?? '');
    $title          = trim($_POST['title'] ?? '');
    $category       = trim($_POST['category'] ?? '');
    $retention      = trim($_POST['retention'] ?? '');
    $establish_date = trim($_POST['establish_date'] ?? '');
    $department     = trim($_POST['department'] ?? '');
    $pic            = trim($_POST['pic'] ?? '');
    $remarks        = trim($_POST['remarks'] ?? '');

    // Validate and upload file
    if (
        empty($document_no) || empty($revision) || empty($title) || empty($category) ||
        empty($retention) || empty($establish_date) || empty($department) || empty($pic) ||
        empty($_FILES['file']['name'])
    ) {
        $_SESSION['error'] = "Please fill in all required fields.";
    } else {
        $allowed_types = ['pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_types)) {
            $_SESSION['error'] = "Invalid file type. Only PDF and Word documents are allowed.";
        } else {
            $safeFilename = uniqid('doc_') . '.' . $ext;
            $safeDepartment = preg_replace('/[^A-Za-z0-9_\- ]/', '', $department);
            $safeDepartment = str_replace(' ', '_', $safeDepartment);

            $targetDir = __DIR__ . '/../uploads/' . $category . '/' . $safeDepartment . '/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            $targetFile = $targetDir . $safeFilename;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                $mysqli = db_connect();
                $stmt = $mysqli->prepare("INSERT INTO documents (document_no, revision, establish_date, retention, title, filename, category, department, pic, uploaded_by, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssssss", $document_no, $revision, $establish_date, $retention, $title, $safeFilename, $category, $department, $pic, $user['id'], $remarks);
                $stmt->execute();
                log_action($user['id'], "Uploaded document: $title", $mysqli->insert_id);

                $_SESSION['message'] = "Document uploaded successfully.";
                header("Location: index.php");
                exit;
            } else {
                $_SESSION['error'] = "File upload failed.";
            }
        }
    }
}

include_once __DIR__ . '/../templates/header.php';
?>
<style>

/* Custom style for smaller font size in the upload form */
.upload-form-small {
    font-size: 0.92rem; /* Decrease base font size */
}
.upload-form-small label,
.upload-form-small .form-label {
    font-size: 0.95em; /* Slightly smaller labels */
}
.upload-form-small textarea,
.upload-form-small input,
.upload-form-small select {
    font-size: 0.95em;
}
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-12">
            <h4 class="mb-4 upload-form">Upload New Document</h4>
            <?php include_once __DIR__ . '/../templates/messages.php'; ?>

            <form method="post" enctype="multipart/form-data" class="bg-white p-3 rounded shadow-sm upload-form-small">
                <input type="hidden" name="csrf_token" value="<?=csrf_token();?>">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">File (PDF, DOC, DOCX) *</label>
                        <input type="file" id="file" name="file" class="form-control form-control-sm" accept=".pdf,.doc,.docx" required>
                    </div>				
                    <div class="col-6 col-lg-2">
                        <label class="form-label">Document No *</label>
                        <input type="text" id="document_no" name="document_no" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-6 col-lg-1">
                        <label class="form-label">Revision *</label>
                        <input type="text" id="revision" name="revision" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Document Name *</label>
                        <input type="text" id="title" name="title" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label">Effective Date *</label>
                        <input type="date" name="establish_date" class="form-control form-control-sm" required>
                    </div>				
                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label">Retention (Years) *</label>
                        <input type="number" name="retention" class="form-control form-control-sm" min="1" max="99" required>
                    </div>				
                    <div class="col-12 col-md-4 col-lg-2">
                        <label class="form-label">Department *</label>
                        <select name="department" class="form-select form-select-sm" required>
                            <option value="">-- Select --</option>
                            <?php foreach($departments as $val=>$label): ?>
                                <option value="<?=$val;?>"><?=$label;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label class="form-label">Prepared by *</label>
                        <input type="text" name="pic" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <label class="form-label">Category *</label>
                        <select name="category" class="form-select form-select-sm" required>
                            <option value="">-- Select --</option>
                            <?php foreach($categories as $val=>$label): ?>
                                <option value="<?=$val;?>"><?=$label;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <label class="form-label">Latest Changes</label>
                        <textarea name="remarks" class="form-control form-control-sm" rows="6"></textarea>
                    </div>
                </div>
                <div class="d-flex mt-4 gap-2">
                    <button type="submit" class="btn btn-success btn-sm flex-fill">Upload Document</button>
                    <a href="javascript:window.history.back();" class="btn btn-secondary btn-sm flex-fill">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('file').addEventListener('change', function() {
    if (!this.files.length) return;

    // Remove file extension
    const fileName = this.files[0].name.replace(/\.[^/.]+$/, "");

    const regex = /^(.+?)_(\d+)_(.+)$/;
    const match = fileName.match(regex);
    if (match) {
        document.getElementById('document_no').value = match[1];
        document.getElementById('revision').value = match[2];
        document.getElementById('title').value = match[3]
            .replace(/_/g, ' ')
            .split(' ')
            .map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase())
            .join(' ');
    }
});
</script>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>