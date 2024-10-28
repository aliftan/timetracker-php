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
                        <th class="w-8 px-6 py-3"></th>
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
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <button onclick="toggleTimerHistory(<?php echo $task['id']; ?>)"
                                    class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5 transform transition-transform duration-200"
                                        id="expand-icon-<?php echo $task['id']; ?>"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </td>
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
                                    <span>Total: <?php echo TimeFormatter::formatDuration($task['tracked_seconds']); ?></span>
                                    <span>Est: <?php echo TimeFormatter::formatDuration($task['estimated_time'] * 60); // Convert minutes to seconds 
                                                ?></span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                                <a href="#"
                                    onclick="startTimer(<?php echo $task['id']; ?>); return false;"
                                    class="text-green-600 hover:text-green-900">
                                    Start Timer
                                </a>
                                <a href="/timetracker-php/projects/<?php echo $project['id']; ?>/tasks/<?php echo $task['id']; ?>/edit"
                                    class="text-blue-600 hover:text-blue-800">
                                    Edit
                                </a>
                                <button onclick="deleteTask(<?php echo $task['id']; ?>)"
                                    class="text-red-600 hover:text-red-800">
                                    Delete
                                </button>
                            </td>
                        </tr>

                        <!-- Timer History Row -->
                        <tr id="timer-history-<?php echo $task['id']; ?>" class="hidden bg-gray-50">
                            <td colspan="6" class="px-6 py-4">
                                <div class="space-y-4">
                                    <h4 class="text-sm font-medium text-gray-700">Timer History</h4>
                                    <?php if (!empty($task['timer_history'])): ?>
                                        <div class="space-y-2">
                                            <?php foreach ($task['timer_history'] as $timer): ?>
                                                <div class="flex justify-between items-center text-sm">
                                                    <div class="text-gray-600">
                                                        <?php echo date('M j, Y g:i A', strtotime($timer['start_time'])); ?>
                                                        →
                                                        <?php echo $timer['end_time'] ? date('g:i A', strtotime($timer['end_time'])) : 'Running'; ?>
                                                    </div>
                                                    <div class="text-gray-900 font-medium">
                                                        <?php echo TimeFormatter::formatDuration($timer['duration_seconds']); ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-sm text-gray-500">No timer history available</p>
                                    <?php endif; ?>
                                </div>
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

<script>
    function toggleTimerHistory(taskId) {
        const historyRow = document.getElementById(`timer-history-${taskId}`);
        const expandIcon = document.getElementById(`expand-icon-${taskId}`);

        if (historyRow.classList.contains('hidden')) {
            historyRow.classList.remove('hidden');
            expandIcon.classList.add('rotate-180');
        } else {
            historyRow.classList.add('hidden');
            expandIcon.classList.remove('rotate-180');
        }
    }

    function startTimer(taskId) {
        fetch(`/timetracker-php/tasks/${taskId}/timer/start`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh the page or show a success message
                    window.location.reload();
                } else {
                    alert(data.error || 'Could not start timer');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Could not start timer');
            });
    }


    function deleteTask(taskId) {
        if (!confirm('Are you sure you want to delete this task?')) {
            return;
        }

        fetch(`/timetracker-php/tasks/${taskId}/delete`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.error || 'Could not delete task');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Could not delete task');
            });
    }
</script>