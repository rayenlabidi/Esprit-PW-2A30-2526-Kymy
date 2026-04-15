<?php

require_once __DIR__ . '/../models/Role.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends BaseController
{
    public function login(): void
    {
        if (is_logged_in()) {
            redirect(['module' => 'dashboard', 'action' => 'index']);
        }

        if (is_post() && $this->db) {
            $userModel = new User($this->db);
            $user = $userModel->authenticate($_POST['email'] ?? '', $_POST['password'] ?? '');

            if ($user) {
                $_SESSION['auth_user'] = [
                    'id' => $user['id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'role_id' => $user['role_id'],
                    'role_name' => $user['role_name'],
                    'role_slug' => $user['role_slug'],
                ];

                set_flash('success', 'Connexion reussie. Bienvenue sur Workify.');
                redirect(['module' => 'dashboard', 'action' => 'index']);
            }

            set_flash('error', 'Email ou mot de passe incorrect.');
            redirect(['module' => 'auth', 'action' => 'login']);
        }

        $this->render('auth/login');
    }

    public function register(): void
    {
        if (is_logged_in()) {
            redirect(['module' => 'dashboard', 'action' => 'index']);
        }

        $roles = [];

        if ($this->db) {
            $roleModel = new Role($this->db);
            $roles = array_filter($roleModel->all(), fn ($role) => $role['slug'] !== 'admin');
        }

        if (is_post() && $this->db) {
            $roleModel = new Role($this->db);
            $userModel = new User($this->db);
            $selectedRole = $roleModel->bySlug($_POST['role_slug'] ?? 'freelancer');

            if (!$selectedRole) {
                $selectedRole = $roleModel->bySlug('freelancer');
            }

            $existingUser = $userModel->findByEmail($_POST['email'] ?? '');

            if ($existingUser) {
                set_flash('error', 'Cet email existe deja.');
                redirect(['module' => 'auth', 'action' => 'register']);
            }

            $userId = $userModel->create([
                'role_id' => $selectedRole['id'],
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'headline' => $_POST['headline'] ?? '',
                'bio' => $_POST['bio'] ?? '',
                'avatar_url' => $_POST['avatar_url'] ?? '',
                'status' => 'active',
            ]);

            $newUser = $userModel->find($userId);
            $_SESSION['auth_user'] = [
                'id' => $newUser['id'],
                'first_name' => $newUser['first_name'],
                'last_name' => $newUser['last_name'],
                'email' => $newUser['email'],
                'role_id' => $newUser['role_id'],
                'role_name' => $newUser['role_name'],
                'role_slug' => $newUser['role_slug'],
            ];

            set_flash('success', 'Compte cree avec succes.');
            redirect(['module' => 'dashboard', 'action' => 'index']);
        }

        $this->render('auth/register', compact('roles'));
    }

    public function logout(): void
    {
        unset($_SESSION['auth_user']);
        set_flash('success', 'Vous etes maintenant deconnecte.');
        redirect(['module' => 'auth', 'action' => 'login']);
    }
}
