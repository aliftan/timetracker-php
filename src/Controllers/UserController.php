<?php
class UserController extends BaseController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function login()
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (Auth::login($email, $password)) {
                $this->redirect('/dashboard');
            } else {
                $this->view('auth/login', [
                    'error' => 'Invalid credentials',
                    'email' => $email
                ]);
            }
        }

        $this->view('auth/login');
    }

    public function register()
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator($_POST);
            $validator->required('username')
                ->required('email')->email('email')
                ->required('password')->min('password', 6);

            if (!$validator->hasErrors()) {
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password']
                ];

                if ($this->user->create($data)) {
                    Auth::login($data['email'], $data['password']);
                    $this->redirect('/dashboard');
                } else {
                    $this->view('auth/register', [
                        'error' => 'Registration failed',
                        'data' => $data
                    ]);
                }
            } else {
                $this->view('auth/register', [
                    'errors' => $validator->getErrors(),
                    'data' => $_POST
                ]);
            }
        }

        $this->view('auth/register');
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('/login');
    }
}
