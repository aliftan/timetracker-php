<?php
$success = Session::getFlash('success');
$error = Session::getFlash('error');
$info = Session::getFlash('info');
$warning = Session::getFlash('warning');
?>

<?php if ($success): ?>
<div class="mb-4 rounded-lg bg-green-100 px-6 py-5 text-base text-green-700" role="alert">
    <?php echo htmlspecialchars($success); ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="mb-4 rounded-lg bg-red-100 px-6 py-5 text-base text-red-700" role="alert">
    <?php echo htmlspecialchars($error); ?>
</div>
<?php endif; ?>

<?php if ($info): ?>
<div class="mb-4 rounded-lg bg-blue-100 px-6 py-5 text-base text-blue-700" role="alert">
    <?php echo htmlspecialchars($info); ?>
</div>
<?php endif; ?>

<?php if ($warning): ?>
<div class="mb-4 rounded-lg bg-yellow-100 px-6 py-5 text-base text-yellow-700" role="alert">
    <?php echo htmlspecialchars($warning); ?>
</div>
<?php endif; ?>

<?php if (!empty($errors) && is_array($errors)): ?>
<div class="mb-4 rounded-lg bg-red-100 px-6 py-5 text-base text-red-700" role="alert">
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $errorMsg): ?>
            <li><?php echo htmlspecialchars($errorMsg); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>