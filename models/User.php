<?php
class User {
    private mysqli $db;

    public function __construct(mysqli $connection) {
        $this->db = $connection;
    }

    public function findByUsernameOrEmail(string $login) {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, username, password_hash, role FROM users WHERE username = ? OR email = ? LIMIT 1'
        );
        $stmt->bind_param('ss', $login, $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    public function emailOrUsernameExists(string $email, string $username): bool {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1');
        $stmt->bind_param('ss', $email, $username);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function createCustomer(string $name, string $email, string $phone, string $username, string $password): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'customer';
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, phone, username, password_hash, role) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('ssssss', $name, $email, $phone, $username, $hash, $role);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
