<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <?php if (isset($project)): ?>
                    Tasks for <?php echo htmlspecialchars($project['name']); ?>
                <?php else: ?>
                    All Tasks
                <?php endif; ?>
            </h1>
            <?php if (isset($project)): ?>
                <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($project['description']); ?></p>
            <?php endif; ?>
        </div>

        <?php if (isset($project)): ?>
            <a href="/timetracker-php/projects/<?php echo $project['id']; ?>/tasks/create"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                New Task
            </a>
        <?php endif; ?>
    </div>

    <!-- Tasks List -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <?php if (!empty($tasks)): ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                        <?php if (!isset($project)): ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($task['name']); ?>
                                </div>
                                <?php if (!empty($task['description'])): ?>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($task['description']); ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <?php if (!isset($project)): ?>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo htmlspecialchars($task['project_name']); ?>
                                </td>
                            <?php endif; ?>

                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo match ($task['status']) {
                                        'completed' => 'bg-green-100 text-green-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    }; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div class="flex flex-col">
                                    <span>Tracked: <?php echo number_format($task['tracked_hours'], 1); ?>h</span>
                                    <span>Est: <?php echo number_format($task['estimated_hours'], 1); ?>h</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                                <a href="/timetracker-php/tasks/<?php echo $task['id']; ?>/timer"
                                    class="text-green-600 hover:text-green-900">Start Timer</a>
                                <a href="/timetracker-php/tasks/<?php echo $task['id']; ?>/edit"
                                    class="text-blue-600 hover:text-blue-900">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-center py-12">
                <p class="text-gray-600">No tasks found.</p>
                <?php if (isset($project)): ?>
                    <a href="/timetracker-php/projects/<?php echo $project['id']; ?>/tasks/create"
                        class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                        Create your first task
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>