<?php

require_once __DIR__ . '/../models/Role.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/Application.php';

class UserController extends BaseController
{
    public function index(): void
    {
        require_roles(['admin']);

        $users = [];
        $roleStats = [];

        if ($this->db) {
            $userModel = new User($this->db);
            $users = $userModel->all();
            $roleStats = $userModel->roleDistribution();
        }

        $this->render('users/index', compact('users', 'roleStats'));
    }

    public function create(): void
    {
        require_roles(['admin']);
        $roles = [];

        if ($this->db) {
            $roleModel = new Role($this->db);
            $roles = $roleModel->all();
        }

        if (is_post() && $this->db) {
            $userModel = new User($this->db);
            $userModel->create([
                'role_id' => $_POST['role_id'] ?? null,
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'headline' => $_POST['headline'] ?? '',
                'bio' => $_POST['bio'] ?? '',
                'avatar_url' => $_POST['avatar_url'] ?? '',
                'status' => $_POST['status'] ?? 'active',
            ]);

            set_flash('success', 'Utilisateur ajoute avec succes.');
            redirect(['module' => 'users', 'action' => 'index']);
        }

        $mode = 'create';
        $user = null;
        $this->render('users/form', compact('roles', 'mode', 'user'));
    }

    public function edit(): void
    {
        require_roles(['admin']);

        $id = (int) ($_GET['id'] ?? 0);
        $roles = [];
        $user = null;

        if ($this->db) {
            $roleModel = new Role($this->db);
            $userModel = new User($this->db);
            $roles = $roleModel->all();
            $user = $userModel->find($id);
        }

        if (!$user) {
            set_flash('error', 'Utilisateur introuvable.');
            redirect(['module' => 'users', 'action' => 'index']);
        }

        if (is_post() && $this->db) {
            $userModel = new User($this->db);
            $userModel->update($id, [
                'role_id' => $_POST['role_id'] ?? null,
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'headline' => $_POST['headline'] ?? '',
                'bio' => $_POST['bio'] ?? '',
                'avatar_url' => $_POST['avatar_url'] ?? '',
                'status' => $_POST['status'] ?? 'active',
            ]);

            set_flash('success', 'Utilisateur modifie avec succes.');
            redirect(['module' => 'users', 'action' => 'index']);
        }

        $mode = 'edit';
        $this->render('users/form', compact('roles', 'mode', 'user'));
    }

    public function delete(): void
    {
        require_roles(['admin']);
        $id = (int) ($_GET['id'] ?? 0);

        if ($this->db && $id > 0) {
            $currentUser = auth_user();

            if ($currentUser && $currentUser['id'] === $id) {
                set_flash('error', 'Vous ne pouvez pas supprimer le compte connecte.');
                redirect(['module' => 'users', 'action' => 'index']);
            }

            $userModel = new User($this->db);
            $userModel->delete($id);
            set_flash('success', 'Utilisateur supprime avec succes.');
        }

        redirect(['module' => 'users', 'action' => 'index']);
    }

    public function show(): void
    {
        require_auth();

        $id = (int) ($_GET['id'] ?? (auth_user()['id'] ?? 0));

        if (!has_role(['admin']) && $id !== (int) auth_user()['id']) {
            set_flash('error', 'Vous pouvez seulement consulter votre propre profil.');
            redirect(['module' => 'users', 'action' => 'show', 'id' => auth_user()['id']]);
        }

        $user = null;
        $enrollments = [];
        $applications = [];

        if ($this->db) {
            $userModel = new User($this->db);
            $enrollmentModel = new Enrollment($this->db);
            $applicationModel = new Application($this->db);

            $user = $userModel->find($id);
            $enrollments = $enrollmentModel->forUser($id);
            $applications = $applicationModel->forUser($id);
        }

        if (!$user) {
            set_flash('error', 'Profil introuvable.');
            redirect(['module' => 'dashboard', 'action' => 'index']);
        }

        $this->render('users/show', compact('user', 'enrollments', 'applications'));
    }
}
