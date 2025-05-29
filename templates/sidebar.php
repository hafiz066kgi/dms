<?php
$category = $_GET['category'] ?? '';
function isActive($cat, $current) {
    return ($cat === $current) ? 'active' : '';
}
?>
<aside class="bg-light p-3 mb-4 rounded shadow-sm" style="min-height: 40vh;" aria-label="Document Category Navigation">
    <h6 class="text-primary text-uppercase mb-3">Categories</h6>
    <div class="list-group">
        <a href="/dms/public/index.php"
           class="list-group-item list-group-item-action <?=isActive('', $category)?>">
            <i class="bi bi-folder2-open me-2"></i> All Documents
        </a>
        <a href="/dms/public/index.php?category=manuals"
           class="list-group-item list-group-item-action <?=isActive('manuals', $category)?>">
            <i class="bi bi-journal-text me-2"></i> Manuals
        </a>
        <a href="/dms/public/index.php?category=procedures"
           class="list-group-item list-group-item-action <?=isActive('procedures', $category)?>">
            <i class="bi bi-card-checklist me-2"></i> Procedures
        </a>
        <a href="/dms/public/index.php?category=work_instructions"
           class="list-group-item list-group-item-action <?=isActive('work_instructions', $category)?>">
            <i class="bi bi-clipboard-check me-2"></i> Work Instructions
        </a>
        <a href="/dms/public/index.php?category=policies"
           class="list-group-item list-group-item-action <?=isActive('policies', $category)?>">
            <i class="bi bi-file-earmark-text me-2"></i> Policies
        </a>
        <a href="/dms/public/index.php?category=others"
           class="list-group-item list-group-item-action <?=isActive('others', $category)?>">
            <i class="bi bi-folder me-2"></i> Others
        </a>
    </div>
</aside>
