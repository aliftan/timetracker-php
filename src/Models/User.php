<?php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare('
            INSERT INTO users (username, email, password) 
            VALUES (:username, :email, :password)
        ');

        $stmt->bindValue(':username', $data['username'], SQLITE3_TEXT);
        $stmt->bindValue(':email', $data['email'], SQLITE3_TEXT);
        $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT), SQLITE3_TEXT);

        return $stmt->execute();
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare('
        UPDATE users 
        SET username = :username, email = :email 
        WHERE id = :id
    ');

        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':username', $data['username'], SQLITE3_TEXT);
        $stmt->bindValue(':email', $data['email'], SQLITE3_TEXT);

        return $stmt->execute();
    }

    public function verifyPassword($id, $password)
    {
        $stmt = $this->db->prepare('SELECT password FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        return $user && password_verify($password, $user['password']);
    }

    public function updatePassword($id, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare('UPDATE users SET password = :password WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);

        return $stmt->execute();
    }
}
