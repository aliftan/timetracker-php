<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">My Projects</h1>
        <a href="/timetracker-php/projects/create"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            New Project
        </a>
    </div>

    <!-- Projects Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (!empty($projects)): ?>
            <?php foreach ($projects as $project): ?>
                <div class="bg-white rounded-lg shadow-sm border p-6 space-y-4">
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <?php echo htmlspecialchars($project['name']); ?>
                        </h3>
                        <span class="px-2 py-1 text-xs rounded-full <?php echo $project['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                            <?php echo $project['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </div>

                    <p class="text-gray-600 text-sm">
                        <?php echo htmlspecialchars($project['description'] ?? 'No description'); ?>
                    </p>

                    <div class="border-t pt-4">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Tasks: <?php echo $project['task_count']; ?></span>
                            <span>Completed: <?php echo $project['completed_tasks']; ?></span>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <!-- Primary Action -->
                            <a href="/timetracker-php/projects/<?php echo $project['id']; ?>/tasks"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-150 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                </svg>
                                View Tasks
                            </a>

                            <!-- Secondary Actions Group -->
                            <div class="flex items-center space-x-2">
                                <a href="/timetracker-php/projects/<?php echo $project['id']; ?>/edit"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150 ease-in-out">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Edit
                                </a>

                                <button onclick="deleteProject(<?php echo $project['id']; ?>)"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150 ease-in-out">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-3 text-center py-12 bg-white rounded-lg border">
                <p class="text-gray-600">You don't have any projects yet.</p>
                <a href="/timetracker-php/projects/create"
                    class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                    Create your first project
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function deleteProject(projectId) {
        if (!confirm('Are you sure you want to delete this project? This will also delete all associated tasks and timers.')) {
            return;
        }

        fetch(`/timetracker-php/projects/${projectId}/delete`, {
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
                    alert(data.error || 'Could not delete project');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Could not delete project');
            });
    }
</script>