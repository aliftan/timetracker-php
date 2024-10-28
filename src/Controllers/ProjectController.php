<?php
class ProjectController extends BaseController {
    private $project;
    private $task;
    
    public function __construct() {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $this->project = new Project();
        $this->task = new Task();
    }
    
    public function index() {
        $projects = $this->project->getUserProjects(Auth::user()['id']);
        $this->view('projects/index', [
            'title' => 'My Projects',
            'projects' => $projects
        ]);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator($_POST);
            $validator->required('name');
            
            if (!$validator->hasErrors()) {
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'user_id' => Auth::user()['id']
                ];
                
                if ($this->project->create($data)) {
                    $this->redirect('/projects');
                }
            }
            
            $this->view('projects/create', [
                'title' => 'Create Project',
                'errors' => $validator->getErrors(),
                'data' => $_POST
            ]);
        }
        
        $this->view('projects/create', ['title' => 'Create Project']);
    }
    
    public function edit($id) {
        $project = $this->project->find($id);
        
        if (!$project || $project['user_id'] !== Auth::user()['id']) {
            $this->redirect('/projects');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator($_POST);
            $validator->required('name');
            
            if (!$validator->hasErrors()) {
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];
                
                if ($this->project->update($id, $data)) {
                    $this->redirect('/projects');
                }
            }
            
            $this->view('projects/edit', [
                'title' => 'Edit Project',
                'project' => $project,
                'errors' => $validator->getErrors(),
                'data' => $_POST
            ]);
        }
        
        $this->view('projects/edit', [
            'title' => 'Edit Project',
            'project' => $project
        ]);
    }
