<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Userpdo {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    private $pdo;
    private $isConnected = false;

    public function __construct() {
        try {
            $this->pdo = new PDO('mysql:host=localhost;dbname=classes;charset=utf8', 'root', '');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connexion PDO échouée: " . $e->getMessage());
        }
    }

    public function register($login, $password, $email, $firstname, $lastname) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (:login, :password, :email, :firstname, :lastname)";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':login' => $login,
            ':password' => $hashedPassword,
            ':email' => $email,
            ':firstname' => $firstname,
            ':lastname' => $lastname
        ]);

        if ($result) {
            $this->id = $this->pdo->lastInsertId();
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            $this->isConnected = true;

            return $this->getAllInfos();
        }
        return false;
    }

    public function connect($login, $password) {
        $sql = "SELECT * FROM utilisateurs WHERE login = :login";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':login' => $login]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->login = $user['login'];
            $this->email = $user['email'];
            $this->firstname = $user['firstname'];
            $this->lastname = $user['lastname'];
            $this->isConnected = true;
            return true;
        }
        return false;
    }

    public function disconnect() {
        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
        $this->isConnected = false;
    }

    public function delete() {
        if ($this->isConnected && $this->id) {
            $sql = "DELETE FROM utilisateurs WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([':id' => $this->id])) {
                $this->disconnect();
                return true;
            }
        }
        return false;
    }

    public function update($login, $password, $email, $firstname, $lastname) {
        if (!$this->isConnected || !$this->id) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE utilisateurs SET login = :login, password = :password, email = :email, firstname = :firstname, lastname = :lastname WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':login' => $login,
            ':password' => $hashedPassword,
            ':email' => $email,
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':id' => $this->id
        ]);

        if ($result) {
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            return true;
        }
        return false;
    }

    public function isConnected() {
        return $this->isConnected;
    }

    public function getAllInfos() {
        if (!$this->isConnected) {
            return null;
        }
        return [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname
        ];
    }

    public function getLogin() {
        return $this->login;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function getLastname() {
        return $this->lastname;
    }
}

// // Exemple de test simple à la fin du fichier 
 $user = new Userpdo();
 $user->register("Tom13", "azerty", "thomas@gmail.com", "Thomas", "DUPONT");
var_dump($user->getAllInfos());

?>
