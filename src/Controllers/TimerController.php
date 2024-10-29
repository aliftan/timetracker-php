<?php
class TimerController extends BaseController
{
    private $timer;
    private $task;

    public function __construct()
    {
        // Use base controller auth check
        $this->requireAuth();

        $this->timer = new Timer();
        $this->task = new Task();
    }

    public function start($taskId)
    {
        error_log("TimerController::start called for taskId: " . $taskId);

        if (!$this->validateMethod('POST')) {
            return;
        }

        $task = $this->validateTaskAccess($taskId);
        if (!$task) {
            return;
        }

        if ($this->timer->startTimer($taskId, Auth::user()['id'])) {
            Session::setFlash('success', 'Timer started...');
            $this->json([
                'success' => true,
                'redirect' => "/timetracker-php/projects/{$task['project_id']}/tasks"
            ]);
        } else {
            Session::setFlash('error', 'Could not start timer');
            $this->json([
                'success' => false,
                'redirect' => "/timetracker-php/projects/{$task['project_id']}/tasks"
            ]);
        }
    }

    public function stop($timerId)
    {
        error_log("TimerController::stop called for timerId: " . $timerId);

        if (!$this->validateMethod('POST')) {
            return;
        }

        if ($this->timer->stopTimer($timerId)) {
            Session::setFlash('success', 'Timer stopped successfully');
            $this->json([
                'success' => true,
                'redirect' => $_SERVER['HTTP_REFERER'] ?? '/timetracker-php/dashboard'
            ]);
        } else {
            Session::setFlash('error', 'Could not stop timer');
            $this->json([
                'success' => false,
                'redirect' => $_SERVER['HTTP_REFERER'] ?? '/timetracker-php/dashboard'
            ]);
        }
    }

    public function current()
    {
        $activeTimer = $this->timer->getActiveTimer(Auth::user()['id']);

        $this->json([
            'success' => true,
            'timer' => $activeTimer
        ]);
    }

    /**
     * Validate task existence and access
     */
    private function validateTaskAccess($taskId)
    {
        $task = $this->task->find($taskId);

        if (!$task) {
            $this->handleErrorResponse('Task not found', '/dashboard');
            return false;
        }

        // Additional project access check could be added here if needed
        // For example, checking if the task belongs to user's project

        return $task;
    }
}
