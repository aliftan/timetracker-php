<?php
class TimerController extends BaseController {
    private $timer;
    private $task;
    
    public function __construct() {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $this->timer = new Timer();
        $this->task = new Task();
    }
    
    public function start($taskId) {
        $task = $this->task->find($taskId);
        if (!$task) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Task not found']);
            }
            $this->redirect('/dashboard');
        }
        
        // Start the timer
        if ($this->timer->startTimer($taskId, Auth::user()['id'])) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => true]);
            }
            $this->redirect("/projects/{$task['project_id']}/tasks");
        }
        
        if ($this->isAjaxRequest()) {
            $this->json(['error' => 'Could not start timer']);
        }
        $this->redirect("/projects/{$task['project_id']}/tasks");
    }
    
    public function stop($timerId) {
        if ($this->timer->stopTimer($timerId)) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => true]);
            }
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/dashboard');
        }
        
        if ($this->isAjaxRequest()) {
            $this->json(['error' => 'Could not stop timer']);
        }
        $this->redirect('/dashboard');
    }
    
    public function current() {
        $activeTimer = $this->timer->getActiveTimer(Auth::user()['id']);
        $this->json([
            'success' => true,
            'timer' => $activeTimer
        ]);
    }
    
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}