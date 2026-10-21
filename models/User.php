<?php
class User {
    private $conn;

    public function __construct($databaseConnection) {
        $this->conn = $databaseConnection;
    }

    // Check whether an email or username is already in the database.
    public function accountExists($email, $username) {
        $sql = "SELECT id FROM users WHERE email = ? OR username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();

        $exists = false;
        if ($stmt->num_rows > 0) {
            $exists = true;
        }

        $stmt->close();
        return $exists;
    }

    // Save a new customer account.
    public function addCustomer($name, $email, $phone, $username, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $role = "customer";

        $sql = "INSERT INTO users
                (name, email, phone, username, password_hash, role)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssssss",
            $name,
            $email,
            $phone,
            $username,
            $passwordHash,
            $role
        );

        $saved = $stmt->execute();
        $stmt->close();

        return $saved;
    }

    // Find a user by their username or email.
    public function findUser($loginName) {
        $sql = "SELECT id, name, email, username, password_hash, role
                FROM users
                WHERE username = ? OR email = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $loginName, $loginName);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        return $user;
    }
}
