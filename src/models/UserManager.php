<?php

class UserManager {

    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Vérifie si un email existe déjà */
    public function userExists($email) {
        $q = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $q->execute([$email]);
        return $q->fetch() ? true : false;
    }

    /** Vérifie si un login existe déjà */
    public function loginExists($login) {
        $q = $this->pdo->prepare("SELECT id FROM users WHERE login = ?");
        $q->execute([$login]);
        return $q->fetch() ? true : false;
    }

    /** Création d'un utilisateur */
    public function createUser($name, $login, $email, $password, $token) {
        // Normalisation des données
        $email = strtolower(trim($email));
        $login = strtolower(trim($login));
        // Sécurité : vérifier format login
        if (!preg_match('/^[a-z][a-z0-9]{2,19}$/', $login)) {
            throw new Exception("Format du login invalide.");
        }
        // Sécurité : vérifier format nom
        if (!preg_match('/^[\p{L} \'-]{2,50}$/u', $name)) {
            throw new Exception("Nom invalide.");
        }
        // Sécurité : vérifier longueur mot de passe
        if (strlen($password) < 8) throw new Exception("Mot de passe trop court.");
        // Sécurité : vérifier longueurs des champs
        if (strlen($email) > 150) throw new Exception("Email trop long.");
        if (strlen($login) > 30) throw new Exception("Login trop long.");

        $sql = "INSERT INTO users (name, login, email, password, activation_token, activation_expires, is_active)
        VALUES (:name, :login, :email, :password, :token, DATE_ADD(NOW(), INTERVAL 24 HOUR), 0)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':login' => $login,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':token' => $token
        ]);
    }

    /** Génération d’un token de reset password */
    public function setResetToken($email, $token) {
        $q = $this->pdo->prepare("
            UPDATE users 
            SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 30 MINUTE)
            WHERE email = ?
        ");
        return $q->execute([$token, $email]);
    }

    /** Vérifier token reset */
    public function validateResetToken($token) {
        $q = $this->pdo->prepare("
            SELECT id, email 
            FROM users 
            WHERE reset_token = ? 
            AND reset_expires > NOW()
        ");
        $q->execute([$token]);
        return $q->fetch();
    }

    /** Mise à jour du mot de passe */
    public function updatePassword($email, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $q = $this->pdo->prepare("
            UPDATE users
            SET password = ?, reset_token = NULL, reset_expires = NULL
            WHERE email = ?
        ");
        return $q->execute([$hash, $email]);
    }

    /** Get user by email */
    public function getUserByEmail($email) {
        $q = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $q->execute([$email]);
        return $q->fetch();
    }

    /** Get user by activation token */
    public function getUserByActivationToken($token) {
        $q = $this->pdo->prepare("
            SELECT * FROM users 
            WHERE activation_token = ?
            AND activation_expires > NOW()
            LIMIT 1
        ");
        $q->execute([$token]);
        return $q->fetch();
    }

    /** Get user by login or email */
    public function getUserByLoginOrEmail($identifier): array|false {
        $q = $this->pdo->prepare("
            SELECT * FROM users 
            WHERE email = :id OR login = :id
            LIMIT 1
        ");
        $q->execute([':id' => $identifier]);
        return $q->fetch();
    }

    /** Activation du compte */
    public function activateUser($email) {
        $q = $this->pdo->prepare("
            UPDATE users 
            SET is_active = 1, activation_token = NULL, activation_expires = NULL
            WHERE email = ?
        ");
        return $q->execute([$email]);
    }

    /** Suppression des comptes non activés depuis plus de 24h */
    public function cleanOldUnactivatedAccounts() {
        $sql = "DELETE FROM users
                WHERE is_active = 0
                AND activation_expires < NOW()";

        return $this->pdo->exec($sql);
    }

}
