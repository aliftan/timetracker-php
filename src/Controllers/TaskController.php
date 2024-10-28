<?php
class TaskController extends BaseController
{
    private $task;
    private $project;
    private $timer;

    public function __construct()
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $this->task = new Task();
        $this->project = new Project();
        $this->timer = new Timer();
    }

    public function index($projectId)
    {
        $project = $this->project->find($projectId);
        if (!$project || $project['user_id'] !== Auth::user()['id']) {
            Session::setFlash('error', 'Project not found');
            $this->redirect('/projects');
        }

        // Get tasks
        $tasks = $this->task->getProjectTasks($projectId);

        // Add timer information to each task
        foreach ($tasks as &$task) {
            $task['tracked_seconds'] = $this->timer->getTrackedSeconds($task['id']);
            $task['timer_history'] = $this->timer->getTimerHistory($task['id']);
        }

        $this->view('tasks/index', [
            'title' => $project['name'] . ' - Tasks',
            'tasks' => $tasks,
            'project' => $project
        ]);
    }

    public function create($projectId)
    {
        $project = $this->project->find($projectId);
        if (!$project || $project['user_id'] !== Auth::user()['id']) {
            Session::setFlash('error', 'Project not found');
            $this->redirect('/projects');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator($_POST);
            $validator->required('name')
                ->required('estimated_time');  // Changed from estimated_hours

            if (!$validator->hasErrors()) {
                $data = [
                    'project_id' => $projectId,
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'estimated_time' => $_POST['estimated_time'],
                    'time_unit' => $_POST['time_unit'] ?? 'minutes',  // Default to minutes
                    'status' => 'pending'
                ];

                if ($this->task->create($data)) {
                    Session::setFlash('success', 'Task created successfully');
                    $this->redirect("/projects/$projectId/tasks");
                } else {
                    Session::setFlash('error', 'Failed to create task');
                }
            }

            $this->view('tasks/create', [
                'title' => 'Create Task',
                'project' => $project,
                'errors' => $validator->getErrors(),
                'data' => $_POST
            ]);
            return;
        }

        $this->view('tasks/create', [
            'title' => 'Create Task',
            'project' => $project
        ]);
    }

    public function edit($taskId)
    {
        $task = $this->task->find($taskId);
        if (!$task) {
            Session::setFlash('error', 'Task not found');
            $this->redirect('/projects');
        }

        $project = $this->project->find($task['project_id']);
        if (!$project || $project['user_id'] !== Auth::user()['id']) {
            Session::setFlash('error', 'Unauthorized');
            $this->redirect('/projects');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator($_POST);
            $validator->required('name')
                ->required('estimated_time');

            if (!$validator->hasErrors()) {
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'estimated_time' => floatval($_POST['estimated_time']),
                    'status' => $_POST['status']
                ];

                if ($this->task->update($taskId, $data)) {
                    Session::setFlash('success', 'Task updated successfully');
                    $this->redirect("/projects/{$project['id']}/tasks");
                } else {
                    Session::setFlash('error', 'Failed to update task');
                }
            }

            $this->view('tasks/edit', [
                'title' => 'Edit Task',
                'task' => $task,
                'project' => $project,
                'errors' => $validator->getErrors(),
                'data' => $_POST
            ]);
            return;
        }

        $this->view('tasks/edit', [
            'title' => 'Edit Task',
            'task' => $task,
            'project' => $project
        ]);
    }

    public function updateStatus($taskId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method']);
            return;
        }

        $task = $this->task->find($taskId);
        if (!$task) {
            $this->json(['error' => 'Task not found']);
            return;
        }

        $project = $this->project->find($task['project_id']);
        if (!$project || $project['user_id'] !== Auth::user()['id']) {
            $this->json(['error' => 'Unauthorized']);
            return;
        }

        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['pending', 'in_progress', 'completed'])) {
            $this->json(['error' => 'Invalid status']);
            return;
        }

        if ($this->task->updateStatus($taskId, $status)) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to update status']);
        }
    }

    public function delete($taskId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method']);
            return;
        }

        $task = $this->task->find($taskId);
        if (!$task) {
            $this->json(['error' => 'Task not found']);
            return;
        }

        $project = $this->project->find($task['project_id']);
        if (!$project || $project['user_id'] !== Auth::user()['id']) {
            $this->json(['error' => 'Unauthorized']);
            return;
        }

        if ($this->task->delete($taskId)) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                $this->json(['success' => true]);
            } else {
                Session::setFlash('success', 'Task deleted successfully');
                $this->redirect("/projects/{$project['id']}/tasks");
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                $this->json(['error' => 'Failed to delete task']);
            } else {
                Session::setFlash('error', 'Failed to delete task');
                $this->redirect("/projects/{$project['id']}/tasks");
            }
        }
    }
}
