<?php

class UserManager {

    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function userExists($email) {
        $q = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $q->execute([$email]);
        return $q->fetch();
    }

    public function createUser($email, $password, $token) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $q = $this->db->prepare("
            INSERT INTO users (email, password, activation_token, is_active)
            VALUES (?, ?, ?, 0)
        ");

        return $q->execute([$email, $hash, $token]);
    }
}
