<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $errorMsg): ?>
                        <li><?php echo htmlspecialchars($errorMsg); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="/timetracker-php/register">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo isset($errors['username']) ? 'border-red-500' : ''; ?>"
                    id="username" type="text" name="username" 
                    value="<?php echo htmlspecialchars($data['username'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo isset($errors['email']) ? 'border-red-500' : ''; ?>"
                    id="email" type="email" name="email" 
                    value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline <?php echo isset($errors['password']) ? 'border-red-500' : ''; ?>"
                    id="password" type="password" name="password" required>
                <p class="text-sm text-gray-600">Password must be at least 6 characters</p>
            </div>
            
            <div class="flex items-center justify-between">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Register
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800"
                    href="/timetracker-php/login">
                    Login
                </a>
            </div>
        </form>
    </div>
</div>