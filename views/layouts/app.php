<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimeTracker - <?php echo $title ?? 'Track Your Time'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/timetracker-php" class="text-2xl font-bold text-blue-600">
                        TimeTracker
                    </a>

                    <?php if (Auth::check()): ?>
                        <div class="ml-10 space-x-4">
                            <a href="/timetracker-php/dashboard" class="text-gray-600 hover:text-blue-600">Dashboard</a>
                            <a href="/timetracker-php/projects" class="text-gray-600 hover:text-blue-600">Projects</a>
                            <a href="/timetracker-php/tasks" class="text-gray-600 hover:text-blue-600">Tasks</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex items-center space-x-4">
                    <?php if (Auth::check()): ?>
                        <span class="text-gray-600">
                            <?php echo htmlspecialchars(Auth::user()['username']); ?>
                        </span>
                        <a href="/timetracker-php/logout"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="/timetracker-php/login" class="text-gray-600 hover:text-blue-600">Login</a>
                        <a href="/timetracker-php/register"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 py-8">
        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-auto">
        <div class="max-w-6xl mx-auto px-4 py-6">
            <div class="text-center text-gray-600">
                &copy; <?php echo date('Y'); ?> TimeTracker. All rights reserved.
            </div>
        </div>
    </footer>
</body>

</html>