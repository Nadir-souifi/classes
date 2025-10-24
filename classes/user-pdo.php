<?php

class Userpdo {
   
    private $id;
    
    
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    
    public function __construct($login = '', $email = '', $firstname = '', $lastname = '') {
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }
    
    
    public function getId() {
        return $this->id;
    }
    
    
    public function setId($id) {
        $this->id = $id;
    }
    
  
    public function register($login, $password, $email, $firstname, $lastname) {
       
        $db = new PDO('mysql:host=localhost;dbname=classes;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
       
        $stmt = $db->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$login, $hashedPassword, $email, $firstname, $lastname]);
        
       
        $userId = $db->lastInsertId();
        
        
        return [
            'id' => $userId,
            'login' => $login,
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname
        ];
    }
    respondantes
    public function connect($login, $password) {
        
        $db = new PDO('mysql:host=localhost;dbname=classes;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
       
        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->login = $user['login'];
            $this->email = $user['email'];
            $this->firstname = $user['firstname'];
            $this->lastname = $user['lastname'];
            return true;
        }
        
        return false;
    }
    
    
    public function disconnect() {
        $this->id = null;
        $this->login = '';
        $this->email = '';
        $this->firstname = '';
        $this->lastname = '';
    }
    
   
    public function delete() {
        if ($this->id) {
        
            $db = new PDO('mysql:host=localhost;dbname=classes;charset=utf8', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
           
            $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->execute([$this->id]);
            
           
            $this->disconnect();
            return true;
        }
        return false;
    }
    base de données
    public function update($login, $password, $email, $firstname, $lastname) {
        if ($this->id) {
            
            $db = new PDO('mysql:host=localhost;dbname=classes;charset=utf8', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
          
            $stmt = $db->prepare("UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
            $stmt->execute([$login, $hashedPassword, $email, $firstname, $lastname, $this->id]);
            
            
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            
            return true;
        }
        return false;
    }
    
   
    public function isConnected() {
        return $this->id !== null;
    }
    
    
   public function getAllInfos() {
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

?>
