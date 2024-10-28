<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md border p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Project</h1>
            <p class="mt-2 text-gray-600">Update your project details and settings.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="rounded-lg bg-red-50 border border-red-200 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
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

        <form method="POST" action="/timetracker-php/projects/<?php echo $project['id']; ?>/edit" class="space-y-8">
            <!-- Project Name -->
            <div class="space-y-2">
                <label for="name" class="block text-sm font-semibold text-gray-700">
                    Project Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name"
                    value="<?php echo htmlspecialchars($project['name']); ?>"
                    class="block w-full px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                    required>
                <p class="text-sm text-gray-500">Choose a descriptive name for your project.</p>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label for="description" class="block text-sm font-semibold text-gray-700">
                    Description
                </label>
                <textarea id="description" name="description" rows="4"
                    class="block w-full px-4 py-3 text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"><?php echo htmlspecialchars($project['description'] ?? ''); ?></textarea>
                <p class="text-sm text-gray-500">Add any relevant details about your project.</p>
            </div>

            <!-- Project Status -->
            <div class="space-y-2">
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                        <?php echo $project['is_active'] ? 'checked' : ''; ?>
                        class="w-5 h-5 rounded border-gray-200 text-blue-600 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    <label for="is_active" class="ml-2 block text-sm font-semibold text-gray-700">
                        Project is active
                    </label>
                </div>
                <p class="text-sm text-gray-500">Inactive projects are hidden from the main view.</p>
            </div>

            <!-- Project Stats -->
            <div class="rounded-lg bg-gray-50 border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Project Statistics</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Total Tasks</p>
                        <p class="text-lg font-semibold text-gray-900"><?php echo $project['task_count'] ?? 0; ?></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Completed Tasks</p>
                        <p class="text-lg font-semibold text-gray-900"><?php echo $project['completed_tasks'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="/timetracker-php/projects"
                    class="px-6 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-blue-700 transition-colors">
                    Update Project
                </button>
            </div>
        </form>
    </div>
</div>