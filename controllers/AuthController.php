<?php
class AuthController
{
    private $userModel;

    public function __construct($userModel)
    {
        $this->userModel = $userModel;
    }

    public function register($form)
    {
        $first = trim($form['first_name'] ?? '');
        $last = trim($form['last_name'] ?? '');
        $middle = trim($form['middle_name'] ?? '');
        $birth = trim($form['birthdate'] ?? '');
        $gender = trim($form['gender'] ?? '');
        $email = trim($form['email'] ?? '');
        $phone = trim($form['phone'] ?? '');
        $address = trim($form['address'] ?? '');
        $username = trim($form['username'] ?? '');
        $password = $form['password'] ?? '';
        $confirm = $form['confirm_password'] ?? '';

        if (!$first || !$last || !$email || !$username || !$password) {
            return ['error' => 'Please complete all required fields.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Enter a valid email address.'];
        }

        if (!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username)) {
            return ['error' => 'Username must be 3-50 characters and use letters, numbers, dot, underscore or hyphen.'];
        }

        if (strlen($password) < 8) {
            return ['error' => 'Password must be at least 8 characters.'];
        }

        if ($password !== $confirm) {
            return ['error' => 'Passwords do not match.'];
        }

        if ($birth && $birth > date('Y-m-d')) {
            return ['error' => 'Birthdate cannot be in the future.'];
        }

        if ($this->userModel->accountExists($email, $username)) {
            return ['error' => 'Email or username is already registered.'];
        }

        $created = $this->userModel->addCustomer(
            $first,
            $last,
            $middle,
            $birth ?: null,
            $gender,
            $email,
            $phone,
            $address,
            $username,
            $password
        );

        if ($created) {
            return ['success' => 'Customer account created. You can now log in.'];
        }

        return ['error' => 'Registration failed. Check the database schema and try again.'];
    }

    public function login($form)
    {
        $login = trim($form['login'] ?? '');
        $password = $form['password'] ?? '';

        if (!$login || !$password) {
            return ['error' => 'Enter your username/email and password.'];
        }

        $user = $this->userModel->findUser($login);

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['department'] = $user['department'] ?? '';
            $_SESSION['must_change_password'] = (int) ($user['must_change_password'] ?? 0);

            return ['success' => true];
        }

        return ['error' => 'Incorrect username/email or password.'];
    }

    public function logout()
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
