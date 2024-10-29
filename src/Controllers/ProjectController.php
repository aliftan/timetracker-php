<?php
class ProjectController extends BaseController
{
    private $project;
    private $task;

    public function __construct()
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $this->project = new Project();
        $this->task = new Task();
    }

    public function index()
    {
        $projects = $this->project->getUserProjects(Auth::user()['id']);
        $this->view('projects/index', [
            'title' => 'My Projects',
            'projects' => $projects
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator($_POST);
            $validator->required('name');

            if ($validator->hasErrors()) {
                $this->view('projects/create', [
                    'title' => 'Create Project',
                    'errors' => $validator->getErrors(),
                    'data' => $_POST
                ]);
                return;
            }

            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'] ?? '',
                'user_id' => Auth::user()['id']
            ];

            if ($this->project->create($data)) {
                Session::setFlash('success', 'Project created successfully!');
                $this->redirect('/projects');
            } else {
                Session::setFlash('error', 'Failed to create project');
                $this->view('projects/create', [
                    'title' => 'Create Project',
                    'data' => $_POST
                ]);
                return;
            }
        }

        $this->view('projects/create', ['title' => 'Create Project']);
    }

    public function edit($id)
    {
        $project = $this->project->find($id);
        if (!$this->canAccessProject($project)) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator($_POST);
            $validator->required('name');

            if ($validator->hasErrors()) {
                $this->view('projects/edit', [
                    'title' => 'Edit Project',
                    'project' => $project,
                    'errors' => $validator->getErrors(),
                    'data' => $_POST
                ]);
                return;
            }

            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'] ?? '',
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if ($this->project->update($id, $data)) {
                Session::setFlash('success', 'Project updated successfully!');
                $this->redirect('/projects');
            } else {
                Session::setFlash('error', 'Failed to update project');
                $this->view('projects/edit', [
                    'title' => 'Edit Project',
                    'project' => $project,
                    'data' => $_POST
                ]);
                return;
            }
        }

        $this->view('projects/edit', [
            'title' => 'Edit Project',
            'project' => $project
        ]);
    }

    public function delete($id)
    {
        if (!$this->validateMethod('POST')) {
            return;
        }

        $project = $this->project->find($id);
        if (!$this->canAccessProject($project)) {
            return;
        }

        if ($this->project->delete($id)) {
            Session::setFlash('success', 'Project deleted successfully!');
            if ($this->isAjaxRequest()) {
                $this->json(['success' => true]);
            } else {
                $this->redirect('/projects');
            }
        } else {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Failed to delete project']);
            } else {
                Session::setFlash('error', 'Failed to delete project');
                $this->redirect('/projects');
            }
        }
    }

    private function canAccessProject($project)
    {
        if (!$project || $project['user_id'] !== Auth::user()['id']) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Project not found']);
            } else {
                Session::setFlash('error', 'Project not found');
                $this->redirect('/projects');
            }
            return false;
        }
        return true;
    }
}
