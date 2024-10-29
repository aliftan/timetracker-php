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
        // Redirect if already logged in
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        if ($this->validateMethod('POST')) {
            $data = $this->sanitizeInput($_POST, [
                'email' => 'email',
                'password' => 'string'
            ]);

            // Validate input
            $validator = new Validator($data);
            $validator->required('email')->email('email')
                ->required('password');

            if ($validator->hasErrors()) {
                $this->view('auth/login', [
                    'errors' => $validator->getErrors(),
                    'email' => $data['email'] ?? ''
                ]);
                return;
            }

            // Attempt login
            if (Auth::login($data['email'], $data['password'])) {
                $this->handleSuccessResponse(
                    'Welcome back!',
                    '/dashboard'
                );
            } else {
                Session::setFlash('error', 'Invalid credentials');
                $this->view('auth/login', [
                    'email' => $data['email']
                ]);
                return;
            }
        }

        $this->view('auth/login', [
            'title' => 'Login'
        ]);
    }

    public function register()
    {
        // Redirect if already logged in
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        if ($this->validateMethod('POST')) {
            $data = $this->sanitizeInput($_POST, [
                'username' => 'string',
                'email' => 'email',
                'password' => 'string'
            ]);

            // Validate input
            $validator = new Validator($data);
            $validator->required('username')
                ->required('email')->email('email')->unique('email', 'users', 'Email is already registered')
                ->required('password')->min('password', 6);

            if ($validator->hasErrors()) {
                $this->view('auth/register', [
                    'errors' => $validator->getErrors(),
                    'data' => $data
                ]);
                return;
            }

            // Create user
            $userData = $this->prepareUserData($data);

            if ($this->user->create($userData)) {
                // Auto login after registration
                if (Auth::login($userData['email'], $userData['password'])) {
                    $this->handleSuccessResponse(
                        'Welcome to TimeTracker! Your account has been created.',
                        '/dashboard'
                    );
                }
            } else {
                Session::setFlash('error', 'Registration failed. Please try again.');
                $this->view('auth/register', [
                    'data' => $data
                ]);
                return;
            }
        }

        $this->view('auth/register', [
            'title' => 'Register'
        ]);
    }

    public function logout()
    {
        if ($this->requireAuth()) {
            Auth::logout();
            $this->handleSuccessResponse(
                'You have been logged out successfully',
                '/login',
                'info'
            );
        }
    }

    /**
     * Prepare user data for creation/update
     */
    private function prepareUserData($data)
    {
        return [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password']
        ];
    }
}
