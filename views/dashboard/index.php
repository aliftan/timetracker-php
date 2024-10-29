<?php
// Initialize variables with defaults if not set
$time_stats = $time_stats ?? [
    'today' => ['total_hours' => 0],
    'week' => ['total_hours' => 0],
    'month' => ['total_hours' => 0]
];

$task_stats = $task_stats ?? [
    'total_tasks' => 0,
    'completed_tasks' => 0,
    'in_progress_tasks' => 0,
    'pending_tasks' => 0
];

$project_stats = $project_stats ?? [
    'total_projects' => 0,
    'active_projects' => 0
];

$recent_projects = $recent_projects ?? [];
$recent_tasks = $recent_tasks ?? [];
?>

<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h1 class="text-2xl font-bold text-gray-900">
            Welcome back, <?php echo htmlspecialchars(Auth::user()['username']); ?>!
        </h1>
        <p class="text-gray-600 mt-1">
            Here's an overview of your activities
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Time Tracking Stats -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Time Tracked</h3>
            <div class="space-y-3">
                <div>
                    <div class="text-sm text-gray-600">Today</div>
                    <div class="text-2xl font-bold text-blue-600">
                        <?php echo number_format($time_stats['today']['total_hours'], 1); ?>h
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">This Week</div>
                    <div class="text-2xl font-bold text-blue-600">
                        <?php echo number_format($time_stats['week']['total_hours'], 1); ?>h
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">This Month</div>
                    <div class="text-2xl font-bold text-blue-600">
                        <?php echo number_format($time_stats['month']['total_hours'], 1); ?>h
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Stats -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tasks</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Tasks</span>
                    <span class="font-semibold"><?php echo $task_stats['total_tasks']; ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Completed</span>
                    <span class="text-green-600 font-semibold"><?php echo $task_stats['completed_tasks']; ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">In Progress</span>
                    <span class="text-blue-600 font-semibold"><?php echo $task_stats['in_progress_tasks']; ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Pending</span>
                    <span class="text-yellow-600 font-semibold"><?php echo $task_stats['pending_tasks']; ?></span>
                </div>
            </div>
        </div>

        <!-- Project Stats -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Projects</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Active Projects</span>
                    <span class="font-semibold"><?php echo $project_stats['active_projects']; ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Projects</span>
                    <span class="font-semibold"><?php echo $project_stats['total_projects']; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recent Projects -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Projects</h3>
                <a href="/timetracker-php/projects" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All
                </a>
            </div>
            <?php if (!empty($recent_projects)): ?>
                <div class="space-y-3">
                    <?php foreach ($recent_projects as $project): ?>
                        <a href="/timetracker-php/projects/<?php echo $project['id']; ?>/tasks"
                            class="block p-3 rounded-lg hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium text-gray-900">
                                        <?php echo htmlspecialchars($project['name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <?php echo $project['task_count']; ?> tasks
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $project['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo $project['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-600 text-center py-4">No projects yet</p>
            <?php endif; ?>
        </div>

        <!-- Recent Tasks -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Tasks</h3>
                <a href="/timetracker-php/tasks" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All
                </a>
            </div>
            <?php if (!empty($recent_tasks)): ?>
                <div class="space-y-3">
                    <?php foreach ($recent_tasks as $task): ?>
                        <div class="p-3 rounded-lg hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium text-gray-900">
                                        <?php echo htmlspecialchars($task['name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <?php echo htmlspecialchars($task['project_name']); ?>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    <?php echo match ($task['status']) {
                                        'completed' => 'bg-green-100 text-green-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    }; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                </span>
                            </div>
                            <div class="mt-2 text-sm text-gray-600">
                                <?php echo number_format($task['tracked_hours'], 1); ?>h /
                                <?php echo number_format($task['estimated_hours'], 1); ?>h
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-600 text-center py-4">No tasks yet</p>
            <?php endif; ?>
        </div>
    </div>
</div>