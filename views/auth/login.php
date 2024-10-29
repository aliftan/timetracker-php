<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md border p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Welcome Back!</h1>
            <p class="mt-2 text-gray-600">Sign in to your account to continue.</p>
        </div>

        <form method="POST" action="/timetracker-php/login" class="space-y-8">
            <!-- Email -->
            <div class="space-y-2">
                <label for="email" class="block text-sm font-semibold text-gray-700">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email"
                    value="<?php echo htmlspecialchars($email ?? ''); ?>"
                    class="block w-full px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                    placeholder="Enter your email"
                    required>
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-semibold text-gray-700">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="password" name="password"
                    class="block w-full px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                    placeholder="Enter your password"
                    required>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between space-x-4 pt-6 border-t">
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-blue-700 transition-colors">
                    Sign In
                </button>
                <a href="/timetracker-php/register"
                    class="text-sm text-gray-600 hover:text-gray-900">
                    Don't have an account? Register
                </a>
            </div>
        </form>
    </div>
</div>