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
            $validator = new Validator($_POST);
            $validator->required('email')->email('email')
                ->required('password');

            if (!$validator->hasErrors()) {
                $email = $_POST['email'];
                $password = $_POST['password'];

                if (Auth::login($email, $password)) {
                    Session::setFlash('success', 'Welcome back!');
                    $this->redirect('/dashboard');
                } else {
                    Session::setFlash('error', 'Invalid credentials');
                    $this->view('auth/login', [
                        'email' => $email
                    ]);
                    return;
                }
            } else {
                $this->view('auth/login', [
                    'errors' => $validator->getErrors(),
                    'email' => $_POST['email'] ?? ''
                ]);
                return;
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
                ->required('email')->email('email')->unique('email', 'users', 'Email is already registered')
                ->required('password')->min('password', 6);

            if (!$validator->hasErrors()) {
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password']
                ];

                if ($this->user->create($data)) {
                    Auth::login($data['email'], $data['password']);
                    Session::setFlash('success', 'Welcome to TimeTracker! Your account has been created.');
                    $this->redirect('/dashboard');
                } else {
                    Session::setFlash('error', 'Registration failed. Please try again.');
                    $this->view('auth/register', [
                        'data' => $data
                    ]);
                    return;
                }
            } else {
                $this->view('auth/register', [
                    'errors' => $validator->getErrors(),
                    'data' => $_POST
                ]);
                return;
            }
        }

        $this->view('auth/register');
    }

    public function logout()
    {
        Auth::logout();
        Session::setFlash('info', 'You have been logged out successfully');
        $this->redirect('/login');
    }
}
