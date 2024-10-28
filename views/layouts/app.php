<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimeTracker - <?php echo $title ?? 'Track Your Time'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body class="min-h-screen flex flex-col bg-gray-50">
    <?php require __DIR__ . '/../components/header.php'; ?>

    <main class="flex-grow">
        <div class="container mx-auto px-4 py-8">
            <?php require __DIR__ . '/../components/alerts.php'; ?>
            <?php echo $content; ?>
        </div>
    </main>

    <?php require __DIR__ . '/../components/footer.php'; ?>
    
    <?php if (Auth::check()): ?>
        <?php require __DIR__ . '/../components/timer.php'; ?>
    <?php endif; ?>
</body>
</html>