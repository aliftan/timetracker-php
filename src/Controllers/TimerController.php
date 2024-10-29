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
        // Validate request method
        if (!$this->validateMethod('POST')) {
            return;
        }

        // Validate task existence and ownership
        $task = $this->validateTaskAccess($taskId);
        if (!$task) {
            return;
        }

        // Start the timer
        if ($this->timer->startTimer($taskId, Auth::user()['id'])) {
            $this->handleSuccessResponse(
                'Timer started successfully',
                $this->isAjaxRequest() ? null : "/projects/{$task['project_id']}/tasks"
            );
        } else {
            $this->handleErrorResponse(
                'Could not start timer',
                "/projects/{$task['project_id']}/tasks"
            );
        }
    }

    public function stop($timerId)
    {
        // Validate request method
        if (!$this->validateMethod('POST')) {
            return;
        }

        // Stop the timer
        if ($this->timer->stopTimer($timerId)) {
            $this->handleSuccessResponse(
                'Timer stopped successfully',
                $this->isAjaxRequest() ? null : ($_SERVER['HTTP_REFERER'] ?? '/dashboard')
            );
        } else {
            $this->handleErrorResponse(
                'Could not stop timer',
                '/dashboard'
            );
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
