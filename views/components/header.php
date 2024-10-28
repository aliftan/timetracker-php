<?php
// Add this helper function at the top of the file
function isActive($path)
{
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return strpos($currentPath, $path) !== false ? 'text-blue-600 font-medium' : 'text-gray-600';
}
?>

<nav class="bg-white shadow-lg">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <a href="/timetracker-php" class="text-2xl font-bold text-blue-600">
                TimeTracker
            </a>

            <!-- Navigation links -->
            <div class="flex items-center space-x-6">
                <?php if (Auth::check()): ?>
                    <a href="/timetracker-php/dashboard"
                        class="<?php echo isActive('/dashboard'); ?> hover:text-blue-600 transition-colors relative group">
                        Dashboard
                    </a>

                    <a href="/timetracker-php/projects"
                        class="<?php echo isActive('/projects'); ?> hover:text-blue-600 transition-colors relative group">
                        Projects
                    </a>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            @click.away="open = false"
                            class="flex items-center space-x-2 focus:outline-none">
                            <img src="<?php echo htmlspecialchars(Auth::user()['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()['username'])); ?>"
                                alt="Profile"
                                class="w-8 h-8 rounded-full">
                            <span class="text-gray-700"><?php echo htmlspecialchars(Auth::user()['username']); ?></span>
                            <!-- Dropdown Arrow -->
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                :class="{'rotate-180': open}"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 py-2 bg-white rounded-lg shadow-xl border border-gray-100">

                            <a href="/timetracker-php/profile"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Profile Settings</span>
                            </a>

                            <div class="border-t border-gray-100 my-1"></div>

                            <a href="/timetracker-php/logout"
                                class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/timetracker-php/login"
                        class="text-gray-600 hover:text-blue-600 transition-colors">Login</a>
                    <a href="/timetracker-php/register"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Add Alpine.js for dropdown functionality -->
<script src="//unpkg.com/alpinejs" defer></script>

<style>
    /* Dropdown transition styles */
    .transform {
        --tw-translate-x: 0;
        --tw-translate-y: 0;
        --tw-rotate: 0;
        --tw-skew-x: 0;
        --tw-skew-y: 0;
        --tw-scale-x: 1;
        --tw-scale-y: 1;
        transform: translateX(var(--tw-translate-x)) translateY(var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
    }

    /* Animation classes */
    .transition {
        transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
</style>