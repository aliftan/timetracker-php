<?php ob_start(); ?>

<div class="min-h-[500px] flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
        <p class="text-xl text-gray-600 mb-8">Page not found</p>
        <a href="/" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-200">
            Go Home
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../views/layouts/app.php';
?>