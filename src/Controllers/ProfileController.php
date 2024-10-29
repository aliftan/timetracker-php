<?php
class ProfileController extends BaseController
{
    private $user;

    public function __construct()
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
        $this->user = new User();
    }

    public function index()
    {
        $this->view('profile/index', [
            'title' => 'Profile Settings'
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }

        $validator = new Validator($_POST);
        $validator->required('username')
            ->required('email')->email('email');

        // Check if email is already taken by another user
        if ($_POST['email'] !== Auth::user()['email']) {
            $validator->unique('email', 'users', 'Email is already taken');
        }

        if ($validator->hasErrors()) {
            Session::setFlash('error', 'Please fix the errors below');
            $this->view('profile/index', [
                'title' => 'Profile Settings',
                'errors' => $validator->getErrors()
            ]);
            return;
        }

        $data = [
            'username' => $_POST['username'],
            'email' => $_POST['email']
        ];

        if ($this->user->update(Auth::user()['id'], $data)) {
            Session::setFlash('success', 'Profile updated successfully');
            Auth::refresh(); // Refresh the session with new data
        } else {
            Session::setFlash('error', 'Failed to update profile');
        }

        $this->redirect('/profile');
    }

    public function password()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }

        $validator = new Validator($_POST);
        $validator->required('current_password')
            ->required('new_password')->min('new_password', 6)
            ->required('confirm_password')->match('confirm_password', 'new_password');

        if ($validator->hasErrors()) {
            Session::setFlash('error', 'Please fix the errors below');
            $this->view('profile/index', [
                'title' => 'Profile Settings',
                'errors' => $validator->getErrors()
            ]);
            return;
        }

        // Verify current password
        if (!$this->user->verifyPassword(Auth::user()['id'], $_POST['current_password'])) {
            Session::setFlash('error', 'Current password is incorrect');
            $this->redirect('/profile');
            return;
        }

        if ($this->user->updatePassword(Auth::user()['id'], $_POST['new_password'])) {
            Session::setFlash('success', 'Password changed successfully');
        } else {
            Session::setFlash('error', 'Failed to change password');
        }

        $this->redirect('/profile');
    }
}
