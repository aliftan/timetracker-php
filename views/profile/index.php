<div class="max-w-3xl mx-auto px-4 py-8">
    <!-- Profile Settings -->
    <div class="bg-white rounded-lg shadow-md border p-8 mb-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
            <p class="mt-2 text-gray-600">Update your account information.</p>
        </div>

        <form action="/timetracker-php/profile/update" method="POST" class="space-y-8">
            <!-- Username -->
            <div class="space-y-2">
                <label for="username" class="block text-sm font-semibold text-gray-700">
                    Username <span class="text-red-500">*</span>
                </label>
                <input type="text" id="username" name="username"
                    value="<?php echo htmlspecialchars(Auth::user()['username']); ?>"
                    class="block w-full px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                    required>
                <p class="text-sm text-gray-500">This is your display name on TimeTracker.</p>
            </div>

            <!-- Email -->
            <div class="space-y-2">
                <label for="email" class="block text-sm font-semibold text-gray-700">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email"
                    value="<?php echo htmlspecialchars(Auth::user()['email']); ?>"
                    class="block w-full px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                    required>
                <p class="text-sm text-gray-500">Used for account notifications and recovery.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-blue-700 transition-colors">
                    Update Profile
                </button>
            </div>
        </form>
    </div>

    <!-- Password Change Section -->
    <div class="bg-white rounded-lg shadow-md border p-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Change Password</h2>
            <p class="mt-2 text-gray-600">Ensure your account is secure with a strong password.</p>
        </div>

        <form action="/timetracker-php/profile/password" method="POST" class="space-y-8">
            <!-- Current Password -->
            <div class="space-y-2">
                <label for="current_password" class="block text-sm font-semibold text-gray-700">
                    Current Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="current_password" name="current_password"
                    class="block w-full px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                    required>
            </div>

            <!-- New Password -->
            <div class="space-y-2">
                <label for="new_password" class="block text-sm font-semibold text-gray-700">
                    New Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="new_password" name="new_password"
                    class="block w-full px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                    required>
                <p class="text-sm text-gray-500">Must be at least 6 characters long.</p>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
                <label for="confirm_password" class="block text-sm font-semibold text-gray-700">
                    Confirm New Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="confirm_password" name="confirm_password"
                    class="block w-full px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                    required>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                            <ul class="mt-2 text-sm text-red-700 space-y-1">
                                <?php foreach ($errors as $error): ?>
                                    <li class="flex items-center space-x-2">
                                        <span>•</span>
                                        <span><?php echo htmlspecialchars($error); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-blue-700 transition-colors">
                    Change Password
                </button>
            </div>
        </form>
    </div>

    <!-- Security Tips -->
    <div class="mt-6 bg-blue-50 rounded-lg p-4 border border-blue-100">
        <h3 class="text-sm font-medium text-blue-800">Security Tips:</h3>
        <ul class="mt-2 text-sm text-blue-700 space-y-1">
            <?php foreach (
                [
                    'Use a strong password with a mix of letters, numbers, and symbols',
                    'Never share your password with anyone',
                    'Update your password regularly for better security'
                ] as $tip
            ): ?>
                <li class="flex items-center space-x-2">
                    <span>•</span>
                    <span><?php echo $tip; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>