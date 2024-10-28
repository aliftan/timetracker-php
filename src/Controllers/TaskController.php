<?php
class TaskController extends BaseController {
    private $task;
    private $project;
    
    public function __construct() {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $this->task = new Task();
        $this->project = new Project();
    }
    
    public function index($projectId = null) {
        $user = Auth::user();
        
        if ($projectId) {
            $project = $this->project->find($projectId);
            if (!$project || $project['user_id'] !== $user['id']) {
                $this->redirect('/projects');
            }
            $tasks = $this->task->getProjectTasks($projectId);
            $this->view('tasks/index', [
                'title' => $project['name'] . ' - Tasks',
                'tasks' => $tasks,
                'project' => $project
            ]);
        } else {
            $tasks = $this->task->getUserTasks($user['id']);
            $this->view('tasks/index', [
                'title' => 'All Tasks',
                'tasks' => $tasks
            ]);
        }
    }
    
    public function create($projectId) {
        $project = $this->project->find($projectId);
        if (!$project || $project['user_id'] !== Auth::user()['id']) {
            $this->redirect('/projects');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator($_POST);
            $validator->required('name')
                     ->required('estimated_hours');
            
            if (!$validator->hasErrors()) {
                $data = [
                    'project_id' => $projectId,
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'estimated_hours' => floatval($_POST['estimated_hours']),
                    'status' => 'pending'
                ];
                
                if ($this->task->create($data)) {
                    $this->redirect("/projects/$projectId/tasks");
                }
            }
            
            $this->view('tasks/create', [
                'title' => 'Create Task',
                'project' => $project,
                'errors' => $validator->getErrors(),
                'data' => $_POST
            ]);
        }
        
        $this->view('tasks/create', [
            'title' => 'Create Task',
            'project' => $project
        ]);
    }
    
    public function edit($taskId) {
        $task = $this->task->find($taskId);
        if (!$task) {
            $this->redirect('/projects');
        }
        
        $project = $this->project->find($task['project_id']);
        if ($project['user_id'] !== Auth::user()['id']) {
            $this->redirect('/projects');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator($_POST);
            $validator->required('name')
                     ->required('estimated_hours');
            
            if (!$validator->hasErrors()) {
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'estimated_hours' => floatval($_POST['estimated_hours']),
                    'status' => $_POST['status']
                ];
                
                if ($this->task->update($taskId, $data)) {
                    $this->redirect("/projects/{$project['id']}/tasks");
                }
            }
            
            $this->view('tasks/edit', [
                'title' => 'Edit Task',
                'task' => $task,
                'project' => $project,
                'errors' => $validator->getErrors(),
                'data' => $_POST
            ]);
        }
        
        $this->view('tasks/edit', [
            'title' => 'Edit Task',
            'task' => $task,
            'project' => $project
        ]);
    }

    public function updateStatus($taskId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = $this->task->find($taskId);
            if (!$task) {
                $this->json(['error' => 'Task not found']);
            }
            
            $project = $this->project->find($task['project_id']);
            if ($project['user_id'] !== Auth::user()['id']) {
                $this->json(['error' => 'Unauthorized']);
            }
            
            $status = $_POST['status'] ?? '';
            if (in_array($status, ['pending', 'in_progress', 'completed'])) {
                if ($this->task->updateStatus($taskId, $status)) {
                    $this->json(['success' => true]);
                }
            }
            
            $this->json(['error' => 'Invalid status']);
        }
    }
}