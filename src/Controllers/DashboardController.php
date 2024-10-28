<?php
class DashboardController extends BaseController {
    private $project;
    private $task;
    private $timer;
    
    public function __construct() {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $this->project = new Project();
        $this->task = new Task();
        $this->timer = new Timer();
    }
    
    public function index() {
        $userId = Auth::user()['id'];
        
        $data = [
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

        // Get time tracking stats
        $data['time_stats'] = [
            'today' => $this->timer->getTimerStats($userId, 'today'),
            'week' => $this->timer->getTimerStats($userId, 'week'),
            'month' => $this->timer->getTimerStats($userId, 'month')
        ];

        // Get task statistics
        $data['task_stats'] = $this->task->getTaskStats($userId);

        // Get project statistics
        $data['project_stats'] = $this->project->getProjectStats($userId);

        // Get recent projects
        $data['recent_projects'] = $this->project->getUserProjects($userId, 5);

        // Get recent tasks
        $data['recent_tasks'] = $this->task->getUserTasks($userId, 5);
        
        $this->view('dashboard/index', $data);
    }
}