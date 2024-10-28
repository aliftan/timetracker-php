<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md border p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create New Task</h1>
            <p class="mt-2 text-gray-600">Add a new task to <?php echo htmlspecialchars($project['name']); ?></p>
        </div>

        <form method="POST" class="space-y-8">
            <!-- Task Name -->
            <div class="space-y-2">
                <label for="name" class="block text-sm font-semibold text-gray-700">
                    Task Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name"
                    value="<?php echo htmlspecialchars($data['name'] ?? ''); ?>"
                    class="block w-full px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                    placeholder="Enter task name"
                    required>
                <p class="text-sm text-gray-500">Choose a clear, actionable name for your task.</p>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label for="description" class="block text-sm font-semibold text-gray-700">
                    Description
                </label>
                <textarea id="description" name="description" rows="4"
                    class="block w-full px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                    placeholder="Describe what needs to be done"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
                <p class="text-sm text-gray-500">Add any details or instructions for this task.</p>
            </div>

            <!-- Estimated Time -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                    Estimated Time <span class="text-red-500">*</span>
                </label>
                <div class="flex space-x-4">
                    <div class="flex-1">
                        <input type="number"
                            id="estimated_time"
                            name="estimated_time"
                            value="<?php echo htmlspecialchars($data['estimated_time'] ?? '1'); ?>"
                            min="1"
                            step="1"
                            class="block w-full px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                            required>
                    </div>
                    <select name="time_unit"
                        class="w-32 px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="minutes" <?php echo ($data['time_unit'] ?? '') === 'minutes' ? 'selected' : ''; ?>>Minutes</option>
                        <option value="hours" <?php echo ($data['time_unit'] ?? '') === 'hours' ? 'selected' : ''; ?>>Hours</option>
                    </select>
                </div>
                <p class="text-sm text-gray-500">Enter how long you think this task will take.</p>
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
                <a href="/timetracker-php/projects/<?php echo $project['id']; ?>/tasks"
                    class="px-6 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-blue-700 transition-colors">
                    Create Task
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Tips -->
    <div class="mt-6 bg-blue-50 rounded-lg p-4 border border-blue-100">
        <h3 class="text-sm font-medium text-blue-800">Quick Tips:</h3>
        <ul class="mt-2 text-sm text-blue-700 space-y-1">
            <?php foreach (
                [
                    'Break down complex tasks into smaller, manageable pieces',
                    'Be specific about what needs to be accomplished',
                    'Set realistic time estimates'
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