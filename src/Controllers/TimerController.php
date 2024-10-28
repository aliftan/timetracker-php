<?php
class TimerController extends BaseController
{
    private $timer;
    private $task;

    public function __construct()
    {
        if (!Auth::check()) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Unauthorized']);
            }
            $this->redirect('/login');
        }
        $this->timer = new Timer();
        $this->task = new Task();
    }

    public function start($taskId)
    {
        $task = $this->task->find($taskId);
        if (!$task) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Task not found']);
            } else {
                Session::setFlash('error', 'Task not found');
                $this->redirect('/dashboard');
            }
            return;
        }

        // Start the timer
        if ($this->timer->startTimer($taskId, Auth::user()['id'])) {
            // Always set flash message regardless of request type
            Session::setFlash('success', 'Timer started successfully');

            if ($this->isAjaxRequest()) {
                $this->json([
                    'success' => true,
                    'message' => 'Timer started successfully'
                ]);
            } else {
                $this->redirect("/projects/{$task['project_id']}/tasks");
            }
        } else {
            Session::setFlash('error', 'Could not start timer');

            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Could not start timer']);
            } else {
                $this->redirect("/projects/{$task['project_id']}/tasks");
            }
        }
    }

    public function stop($timerId)
    {
        if ($this->timer->stopTimer($timerId)) {
            // Always set flash message regardless of request type
            Session::setFlash('success', 'Timer stopped successfully');

            if ($this->isAjaxRequest()) {
                $this->json([
                    'success' => true,
                    'message' => 'Timer stopped successfully'
                ]);
            } else {
                $this->redirect($_SERVER['HTTP_REFERER'] ?? '/dashboard');
            }
        } else {
            Session::setFlash('error', 'Could not stop timer');

            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Could not stop timer']);
            } else {
                $this->redirect('/dashboard');
            }
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

    private function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
