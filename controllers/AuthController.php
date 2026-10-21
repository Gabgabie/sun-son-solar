<?php
class AuthController {
    private User $users;

    public function __construct(User $users) {
        $this->users = $users;
    }

    public function register(array $data): array {
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        if ($name === '' || $email === '' || $username === '' || $password === '') {
            return ['error' => 'Please complete all required fields.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Please enter a valid email address.'];
        }
        if (strlen($password) < 8) {
            return ['error' => 'Password must be at least 8 characters long.'];
        }
        if ($password !== $confirmPassword) {
            return ['error' => 'Passwords do not match.'];
        }
        if ($this->users->emailOrUsernameExists($email, $username)) {
            return ['error' => 'That email or username is already registered.'];
        }
        if (!$this->users->createCustomer($name, $email, $phone, $username, $password)) {
            return ['error' => 'Registration failed. Please try again.'];
        }
        return ['success' => 'Account created. You can now log in.'];
    }

    public function login(array $data): array {
        $login = trim($data['login'] ?? '');
        $password = $data['password'] ?? '';
        if ($login === '' || $password === '') {
            return ['error' => 'Enter your username/email and password.'];
        }

        $user = $this->users->findByUsernameOrEmail($login);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['error' => 'Incorrect username/email or password.'];
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        return ['success' => true];
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}
