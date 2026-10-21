<?php
class AuthController {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    public function register($form) {
        $name = trim($form["name"] ?? "");
        $email = trim($form["email"] ?? "");
        $phone = trim($form["phone"] ?? "");
        $username = trim($form["username"] ?? "");
        $password = $form["password"] ?? "";
        $confirmPassword = $form["confirm_password"] ?? "";

        if ($name == "" || $email == "" || $username == "" || $password == "") {
            return array("error" => "Please complete all required fields.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return array("error" => "Please enter a valid email address.");
        }

        if (strlen($password) < 8) {
            return array("error" => "Password must be at least 8 characters.");
        }

        if ($password != $confirmPassword) {
            return array("error" => "Passwords do not match.");
        }

        if ($this->userModel->accountExists($email, $username)) {
            return array("error" => "That email or username is already registered.");
        }

        $saved = $this->userModel->addCustomer(
            $name,
            $email,
            $phone,
            $username,
            $password
        );

        if ($saved) {
            return array("success" => "Account created. You can now log in.");
        }

        return array("error" => "Registration failed. Please try again.");
    }

    public function login($form) {
        $loginName = trim($form["login"] ?? "");
        $password = $form["password"] ?? "";

        if ($loginName == "" || $password == "") {
            return array("error" => "Enter your username/email and password.");
        }

        $user = $this->userModel->findUser($loginName);

        if ($user && password_verify($password, $user["password_hash"])) {
            session_regenerate_id(true);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_role"] = $user["role"];

            return array("success" => true);
        }

        return array("error" => "Incorrect username/email or password.");
    }

    public function logout() {
        $_SESSION = array();
        session_destroy();
    }
}
