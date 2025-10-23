<?php
// Afficher les erreur
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class User {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    private $conn;
    private $isConnected = false;

    // Constructeur : connexion à la base de données
    public function __construct() {
        $this->conn = new mysqli('localhost', 'root', '', 'classes');
        if ($this->conn->connect_error) {
            throw new Exception("Connexion échouée: " . $this->conn->connect_error);
        }
    }

    // Register : création d'un utilisateur
    public function register($login, $password, $email, $firstname, $lastname) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            return ["success" => false, "error" => $this->conn->error];
        }
        $stmt->bind_param("sssss", $login, $hashedPassword, $email, $firstname, $lastname);
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            $this->isConnected = true;
            $stmt->close();
            return $this->getAllInfos();
        } else {
            $err = $stmt->error;
            $stmt->close();
            return ["success" => false, "error" => $err];
        }
    }

    // Connect : connexion utilisateur avec vérification du password
    public function connect($login, $password) {
        $stmt = $this->conn->prepare("SELECT id, login, password, email, firstname, lastname FROM utilisateurs WHERE login = ? LIMIT 1");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $this->id = $user['id'];
                $this->login = $user['login'];
                $this->email = $user['email'];
                $this->firstname = $user['firstname'];
                $this->lastname = $user['lastname'];
                $this->isConnected = true;
                $stmt->close();
                return true;
            }
        }
        $stmt->close();
        $this->disconnect();
        return false;
    }

    // Disconnect : déconnexion
    public function disconnect() {
        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
        $this->isConnected = false;
    }

    // Delete : supprime l'utilisateur connecté
    public function delete() {
        if ($this->isConnected && $this->id) {
            $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("i", $this->id);
            $ok = $stmt->execute();
            $stmt->close();
            if ($ok) {
                $this->disconnect();
                return true;
            }
        }
        return false;
    }

    // Update : mise à jour des infos utilisateur
    public function update($login, $password, $email, $firstname, $lastname) {
        if (!$this->isConnected || !$this->id) {
            return false;
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("sssssi", $login, $hashedPassword, $email, $firstname, $lastname, $this->id);
        $ok = $stmt->execute();
        $stmt->close();
        if ($ok) {
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            return true;
        }
        return false;
    }

    // isConnected : retourne true si connecté
    public function isConnected() {
        return $this->isConnected;
    }

    // getAllInfos : retourne toutes les infos dans un tableau
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

    // Destructeur pour fermer la connexion
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// TEST SIMPLE (à décommenter pour tester)
 $user = new User();
 $infos = $user->register("Tom13", "azerty", "thomas@gmail.com", "Thomas", "DUPONT");
if (is_array($infos) && !isset($infos['success'])) {
    echo "Connexion réussie\n";
}
var_dump($infos);
