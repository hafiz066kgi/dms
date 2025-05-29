<?php if (!empty($_SESSION['message'])): ?>
    <div class="alert alert-info">
        <?= htmlspecialchars($_SESSION['message']) ?>
        <?php unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>
