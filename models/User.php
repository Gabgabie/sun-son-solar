<?php
class User
{
    private $connection;

    public function __construct($databaseConnection)
    {
        $this->connection = $databaseConnection;
    }

    public function accountExists($email, $username)
    {
        $sql = 'SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1';
        $statement = $this->connection->prepare($sql);
        $statement->bind_param('ss', $email, $username);
        $statement->execute();
        $statement->store_result();

        $accountWasFound = $statement->num_rows > 0;
        $statement->close();

        return $accountWasFound;
    }

    public function addCustomer($first, $last, $middle, $birth, $gender, $email, $phone, $address, $username, $password)
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'customer';

        $sql = 'INSERT INTO users (
            first_name, last_name, middle_name, birthdate, gender,
            email, phone, address, username, password_hash, role
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $statement = $this->connection->prepare($sql);
        $statement->bind_param(
            'sssssssssss',
            $first,
            $last,
            $middle,
            $birth,
            $gender,
            $email,
            $phone,
            $address,
            $username,
            $passwordHash,
            $role
        );

        $accountWasAdded = $statement->execute();
        $statement->close();

        return $accountWasAdded;
    }

    public function findUser($login)
    {
        $sql = 'SELECT id, first_name, last_name, email, username, password_hash,
                       role, department, must_change_password
                FROM users
                WHERE (username = ? OR email = ?) AND active = 1
                LIMIT 1';

        $statement = $this->connection->prepare($sql);
        $statement->bind_param('ss', $login, $login);
        $statement->execute();

        $user = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $user;
    }
}
