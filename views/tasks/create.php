<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">
            New Task for <?php echo htmlspecialchars($project['name']); ?>
        </h1>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 text-red-800 p-4 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Task Name
                </label>
                <input type="text" id="name" name="name"
                    value="<?php echo htmlspecialchars($data['name'] ?? ''); ?>"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">
                    Description
                </label>
                <textarea id="description" name="description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
            </div>

            <div>
                <label for="estimated_hours" class="block text-sm font-medium text-gray-700">
                    Estimated Hours
                </label>
                <input type="number" id="estimated_hours" name="estimated_hours"
                    value="<?php echo htmlspecialchars($data['estimated_hours'] ?? '1'); ?>"
                    min="0.1" step="0.1"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="/timetracker-php/projects/<?php echo $project['id']; ?>/tasks"
                    class="text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Create Task
                </button>
            </div>
        </form>
    </div>
</div>