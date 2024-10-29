<?php
class TaskController extends BaseController
{
    private $task;
    private $project;
    private $timer;

    public function __construct()
    {
        $this->requireAuth();

        $this->task = new Task();
        $this->project = new Project();
        $this->timer = new Timer();
    }

    public function index($projectId)
    {
        $project = $this->validateProjectAccess($projectId);
        if (!$project) {
            return;
        }

        $tasks = $this->getTasksWithTimerInfo($projectId);

        $this->view('tasks/index', [
            'title' => $project['name'] . ' - Tasks',
            'tasks' => $tasks,
            'project' => $project
        ]);
    }

    public function create($projectId)
    {
        error_log("TaskController::create called with projectId: " . $projectId);

        // First handle project access
        $project = $this->validateProjectAccess($projectId);
        if (!$project) {
            error_log("Project validation failed for ID: " . $projectId);
            return;
        }
        error_log("Project validation passed for ID: " . $projectId);

        // For POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Processing POST request for task creation");

            $validator = new Validator($_POST);
            $validator->required('name')
                ->required('estimated_time');

            // Handle validation errors
            if ($validator->hasErrors()) {
                error_log("Validation errors: " . print_r($validator->getErrors(), true));
                $this->view('tasks/create', [
                    'title' => 'Create Task',
                    'project' => $project,
                    'errors' => $validator->getErrors(),
                    'data' => $_POST
                ]);
                return;
            }

            $data = $this->prepareTaskData($_POST, $projectId);
            error_log("Prepared task data: " . print_r($data, true));

            // Create task and handle response
            if ($this->task->create($data)) {
                error_log("Task created successfully, redirecting to: /projects/$projectId/tasks");
                Session::setFlash('success', 'Task created successfully');
                $this->redirect("/projects/$projectId/tasks");
            } else {
                error_log("Failed to create task");
                Session::setFlash('error', 'Failed to create task');
                $this->view('tasks/create', [
                    'title' => 'Create Task',
                    'project' => $project,
                    'data' => $_POST
                ]);
                return;
            }
        }

        // Show create form (GET request)
        error_log("Showing create form (GET request)");
        $this->view('tasks/create', [
            'title' => 'Create Task',
            'project' => $project
        ]);
    }

    public function edit($taskId, $projectId = null)
    {
        error_log("TaskController::edit called with taskId: {$taskId}, projectId: {$projectId}");

        // First validate task access
        $task = $this->validateTaskAccess($taskId);
        if (!$task) {
            error_log("Task validation failed for ID: {$taskId}");
            return;
        }
        error_log("Task validation passed for ID: {$taskId}");

        // Then validate project access
        $project = $this->validateProjectAccess($task['project_id'], "/projects/{$task['project_id']}/tasks");
        if (!$project) {
            error_log("Project validation failed for ID: {$task['project_id']}");
            return;
        }
        error_log("Project validation passed for ID: {$task['project_id']}");

        // For POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Processing POST request for task edit");

            $validator = new Validator($_POST);
            $validator->required('name')
                ->required('estimated_time');

            // Handle validation errors
            if ($validator->hasErrors()) {
                error_log("Validation errors: " . print_r($validator->getErrors(), true));
                $this->view('tasks/edit', [
                    'title' => 'Edit Task',
                    'task' => $task,
                    'project' => $project,
                    'errors' => $validator->getErrors(),
                    'data' => $_POST
                ]);
                return;
            }

            $data = $this->prepareTaskData($_POST);
            error_log("Prepared task data for update: " . print_r($data, true));

            // Update task and handle response
            if ($this->task->update($taskId, $data)) {
                error_log("Task updated successfully, redirecting to: /projects/{$project['id']}/tasks");
                Session::setFlash('success', 'Task updated successfully');
                $this->redirect("/projects/{$project['id']}/tasks");
            } else {
                error_log("Failed to update task");
                Session::setFlash('error', 'Failed to update task');
                $this->view('tasks/edit', [
                    'title' => 'Edit Task',
                    'task' => $task,
                    'project' => $project,
                    'data' => $_POST
                ]);
                return;
            }
        }

        // Show edit form (GET request)
        error_log("Showing edit form (GET request)");
        $this->view('tasks/edit', [
            'title' => 'Edit Task',
            'task' => $task,
            'project' => $project
        ]);
    }

    public function updateStatus($taskId)
    {
        if (!$this->validateMethod('POST')) {
            return;
        }

        $task = $this->validateTaskAccess($taskId);
        if (!$task) {
            return;
        }

        $status = $_POST['status'] ?? '';
        if (!$this->isValidStatus($status)) {
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
        if (!$this->validateMethod('POST')) {
            return;
        }

        $task = $this->validateTaskAccess($taskId);
        if (!$task) {
            return;
        }

        if ($this->task->delete($taskId)) {
            $this->handleSuccessResponse(
                'Task deleted successfully',
                $this->isAjaxRequest() ? null : "/projects/{$task['project_id']}/tasks"
            );
        } else {
            $this->handleErrorResponse(
                'Failed to delete task',
                "/projects/{$task['project_id']}/tasks"
            );
        }
    }

    /**
     * Validate project access
     */
    private function validateProjectAccess($projectId, $redirectPath = '/projects')
    {
        $project = $this->project->find($projectId);
        if (!$project || $project['user_id'] !== Auth::user()['id']) {
            // Only redirect if not an AJAX request
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Project not found']);
            } else {
                Session::setFlash('error', 'Project not found');
                $this->redirect($redirectPath);
            }
            return false;
        }
        return $project;
    }

    /**
     * Validate task access
     */
    private function validateTaskAccess($taskId)
    {
        error_log("Validating task access for ID: {$taskId}");

        $task = $this->task->find($taskId);
        if (!$task) {
            error_log("Task not found with ID: {$taskId}");
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Task not found']);
            } else {
                Session::setFlash('error', 'Task not found');
                $this->redirect('/projects');
            }
            return false;
        }

        // Optionally add ownership validation if needed
        if ($task['project_id']) {
            $project = $this->project->find($task['project_id']);
            if (!$project || $project['user_id'] !== Auth::user()['id']) {
                error_log("Task access denied - user not authorized");
                if ($this->isAjaxRequest()) {
                    $this->json(['error' => 'Not authorized to access this task']);
                } else {
                    Session::setFlash('error', 'Not authorized to access this task');
                    $this->redirect('/projects');
                }
                return false;
            }
        }

        error_log("Task access validation successful");
        return $task;
    }

    /**
     * Get tasks with timer information
     */
    private function getTasksWithTimerInfo($projectId)
    {
        $tasks = $this->task->getProjectTasks($projectId);
        foreach ($tasks as &$task) {
            $task['tracked_seconds'] = $this->timer->getTrackedSeconds($task['id']);
            $task['timer_history'] = $this->timer->getTimerHistory($task['id']);
        }
        return $tasks;
    }

    /**
     * Prepare task data for create/update
     */
    private function prepareTaskData($data, $projectId = null)
    {
        $taskData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'estimated_time' => floatval($data['estimated_time']),
            'status' => $data['status'] ?? 'pending',
            'time_unit' => $data['time_unit'] ?? 'minutes'
        ];

        if ($projectId) {
            $taskData['project_id'] = $projectId;
        }

        return $taskData;
    }

    /**
     * Check if status is valid
     */
    private function isValidStatus($status)
    {
        return in_array($status, ['pending', 'in_progress', 'completed']);
    }
}
