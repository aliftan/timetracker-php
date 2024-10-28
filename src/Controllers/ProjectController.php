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

            if (!$validator->hasErrors()) {
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'user_id' => Auth::user()['id']
                ];

                if ($this->project->create($data)) {
                    Session::setFlash('success', 'Project created successfully!');
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

    public function edit($id)
    {
        $project = $this->project->find($id);

        if (!$project || $project['user_id'] !== Auth::user()['id']) {
            Session::setFlash('error', 'Project not found');
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
                    Session::setFlash('success', 'Project updated successfully!');
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

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method']);
            return;
        }

        $project = $this->project->find($id);

        if (!$project || $project['user_id'] !== Auth::user()['id']) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Project not found']);
            } else {
                Session::setFlash('error', 'Project not found');
                $this->redirect('/projects');
            }
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

    private function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
