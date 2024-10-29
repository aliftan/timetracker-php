<?php
class DashboardController extends BaseController
{
    private $project;
    private $task;
    private $timer;

    public function __construct()
    {
        $this->requireAuth();

        $this->project = new Project();
        $this->task = new Task();
        $this->timer = new Timer();
    }

    public function index()
    {
        $userId = Auth::user()['id'];
        $this->view('dashboard/index', $this->getDashboardData($userId));
    }

    /**
     * Get all dashboard data for a user
     */
    private function getDashboardData($userId)
    {
        return [
            'title' => 'Dashboard',
            'time_stats' => $this->getTimeStats($userId),
            'task_stats' => $this->task->getTaskStats($userId),
            'project_stats' => $this->project->getProjectStats($userId),
            'recent_projects' => $this->project->getUserProjects($userId, 5),
            'recent_tasks' => $this->task->getUserTasks($userId, 5)
        ];
    }

    /**
     * Get time tracking statistics
     */
    private function getTimeStats($userId)
    {
        return [
            'today' => $this->timer->getTimerStats($userId, 'today') ?: ['total_hours' => 0],
            'week' => $this->timer->getTimerStats($userId, 'week') ?: ['total_hours' => 0],
            'month' => $this->timer->getTimerStats($userId, 'month') ?: ['total_hours' => 0]
        ];
    }
}
