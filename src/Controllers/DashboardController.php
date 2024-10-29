<?php
class DashboardController extends BaseController
{
    private $project;
    private $task;
    private $timer;

    public function __construct()
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $this->project = new Project();
        $this->task = new Task();
        $this->timer = new Timer();
    }

    public function index()
    {
        $userId = Auth::user()['id'];

        // Set default values first
        $viewData = [
            'title' => 'Dashboard',
            'time_stats' => [
                'today' => ['total_hours' => 0],
                'week' => ['total_hours' => 0],
                'month' => ['total_hours' => 0]
            ],
            'task_stats' => [
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'in_progress_tasks' => 0,
                'pending_tasks' => 0
            ],
            'project_stats' => [
                'total_projects' => 0,
                'active_projects' => 0
            ],
            'recent_projects' => [],
            'recent_tasks' => []
        ];

        // Then override with actual data
        $viewData['time_stats'] = [
            'today' => $this->timer->getTimerStats($userId, 'today'),
            'week' => $this->timer->getTimerStats($userId, 'week'),
            'month' => $this->timer->getTimerStats($userId, 'month')
        ];

        $viewData['task_stats'] = $this->task->getTaskStats($userId);
        $viewData['project_stats'] = $this->project->getProjectStats($userId);
        $viewData['recent_projects'] = $this->project->getUserProjects($userId, 5);
        $viewData['recent_tasks'] = $this->task->getUserTasks($userId, 5);

        $this->view('dashboard/index', $viewData);
    }
}
