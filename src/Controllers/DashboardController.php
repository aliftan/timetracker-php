<?php
class DashboardController extends BaseController {
    public function __construct() {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
    }
    
    public function index() {
        $user = Auth::user();
        $timer = new Timer();
        $project = new Project();
        
        $data = [
            'user' => $user,
            'active_timer' => $timer->getActiveTimer($user['id']),
            'recent_projects' => $project->getUserProjects($user['id'], 5),
            'today_total' => $timer->getTimerStats($user['id'], 'today')
        ];
        
        $this->view('dashboard/index', $data);
    }
}