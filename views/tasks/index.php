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
                                    <span>Est: <?php echo TimeFormatter::formatDuration($task['estimated_time'] * 60); ?></span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- Start Timer Button -->
                                    <button onclick="startTimer(<?php echo $task['id']; ?>)"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 text-sm font-medium rounded-md hover:bg-green-100 transition-colors duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Start Timer
                                    </button>

                                    <!-- Edit Button -->
                                    <a href="/timetracker-php/projects/<?php echo $project['id']; ?>/tasks/<?php echo $task['id']; ?>/edit"
                                        class="inline-flex items-center px-3 py-1.5 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition-colors duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Edit
                                    </a>

                                    <!-- Delete Button -->
                                    <button onclick="deleteTask(<?php echo $task['id']; ?>)"
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
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

    function showAlert(message, type = 'success') {
        // Create alert element
        const alert = document.createElement('div');
        alert.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
        alert.textContent = message;

        // Add to document
        document.body.appendChild(alert);

        // Remove after 3 seconds
        setTimeout(() => {
            alert.remove();
        }, 3000);
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
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.reload();
            });
    }

    function stopTimer(timerId) {
        fetch(`/timetracker-php/timer/${timerId}/stop`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.reload();
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