<?php
// views/welcome.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimeTracker - Track Your Time Efficiently</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-b from-blue-50 to-white">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="/timetracker-php" class="text-2xl font-bold text-blue-600">
                            TimeTracker
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/timetracker-php/login" class="text-gray-600 hover:text-blue-600">Login</a>
                        <a href="/timetracker-php/register" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Register
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="max-w-6xl mx-auto px-4 py-20">
            <div class="text-center">
                <h1 class="text-5xl font-bold text-gray-900 mb-6">
                    Track Your Time, Boost Your Productivity
                </h1>
                <p class="text-xl text-gray-600 mb-12 max-w-2xl mx-auto">
                    Simple, efficient time tracking for freelancers and teams.
                    Know exactly where your time goes.
                </p>
                <div class="flex justify-center space-x-6">
                    <a href="/timetracker-php/register"
                        class="bg-blue-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-blue-700 transition duration-300">
                        Get Started Free
                    </a>
                    <a href="/timetracker-php/login"
                        class="border-2 border-blue-600 text-blue-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-blue-50 transition duration-300">
                        Login
                    </a>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="max-w-6xl mx-auto px-4 py-16">
            <div class="grid md:grid-cols-3 gap-12">
                <!-- Feature 1 -->
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Time Tracking</h3>
                    <p class="text-gray-600">Track time spent on projects and tasks with just one click</p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Project Management</h3>
                    <p class="text-gray-600">Organize your work into projects and tasks</p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Reports & Analytics</h3>
                    <p class="text-gray-600">Get insights into your time usage with detailed reports</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>