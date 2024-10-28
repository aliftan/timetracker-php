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

                        <div class="mt-4 flex space-x-3">
                            <a href="/timetracker-php/projects/<?php echo $project['id']; ?>/tasks"
                                class="text-sm text-blue-600 hover:text-blue-800">
                                View Tasks
                            </a>
                            <a href="/timetracker-php/projects/<?php echo $project['id']; ?>/edit"
                                class="text-sm text-gray-600 hover:text-gray-800">
                                Edit
                            </a>
                            <button onclick="deleteProject(<?php echo $project['id']; ?>)"
                                class="text-sm text-red-600 hover:text-red-800">
                                Delete
                            </button>
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