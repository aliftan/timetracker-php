<?php
class ProfileController extends BaseController
{
    private $user;

    public function __construct()
    {
        $this->requireAuth();
        $this->user = new User();
    }

    public function index()
    {
        $this->view('profile/index', [
            'title' => 'Profile Settings',
            'user' => Auth::user()
        ]);
    }

    public function update()
    {
        if (!$this->validateMethod('POST')) {
            return;
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
                'errors' => $validator->getErrors(),
                'user' => Auth::user()
            ]);
            return;
        }

        $data = $this->prepareProfileData($_POST);

        if ($this->user->update(Auth::user()['id'], $data)) {
            Session::setFlash('success', 'Profile updated successfully');
            Auth::refresh();
        } else {
            Session::setFlash('error', 'Failed to update profile');
        }

        $this->redirect('/profile');
    }

    public function password()
    {
        if (!$this->validateMethod('POST')) {
            return;
        }

        if (!$this->validatePasswordChange($_POST)) {
            return;
        }

        if ($this->user->updatePassword(Auth::user()['id'], $_POST['new_password'])) {
            Session::setFlash('success', 'Password changed successfully');
        } else {
            Session::setFlash('error', 'Failed to change password');
        }

        $this->redirect('/profile');
    }

    private function validatePasswordChange($data)
    {
        $validator = new Validator($data);
        $validator->required('current_password')
            ->required('new_password')->min('new_password', 6)
            ->required('confirm_password')->match('confirm_password', 'new_password');

        if ($validator->hasErrors()) {
            Session::setFlash('error', 'Please fix the errors below');
            $this->view('profile/index', [
                'title' => 'Profile Settings',
                'errors' => $validator->getErrors(),
                'user' => Auth::user()
            ]);
            return false;
        }

        if (!$this->user->verifyPassword(Auth::user()['id'], $data['current_password'])) {
            Session::setFlash('error', 'Current password is incorrect');
            $this->redirect('/profile');
            return false;
        }

        return true;
    }

    private function prepareProfileData($data)
    {
        return [
            'username' => trim($data['username']),
            'email' => trim($data['email'])
        ];
    }
}
