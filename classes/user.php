<?php

class User {
  
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
       
        $db = new mysqli('localhost', 'root', '', 'classes');
        
        
        if ($db->connect_error) {
            die("Erreur de connexion : " . $db->connect_error);
        }
        
       
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        
        $stmt = $db->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $login, $hashedPassword, $email, $firstname, $lastname);
        $stmt->execute();
        
        
        $userId = $db->insert_id;
        
        
        $stmt->close();
        $db->close();
        
        
        return [
            'id' => $userId,
            'login' => $login,
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname
        ];
    }
    
    
    public function connect($login, $password) {
        
        $db = new mysqli('localhost', 'root', '', 'classes');
        
        
        if ($db->connect_error) {
            die("Erreur de connexion : " . $db->connect_error);
        }
        
        
        $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        
        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->login = $user['login'];
            $this->email = $user['email'];
            $this->firstname = $user['firstname'];
            $this->lastname = $user['lastname'];
            
            $stmt->close();
            $db->close();
            return true;
        }
        
        $stmt->close();
        $db->close();
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
            
            $db = new mysqli('localhost', 'root', '', 'classes');
            
          
            if ($db->connect_error) {
                die("Erreur de connexion : " . $db->connect_error);
            }
            
           
            $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            
            
            $stmt->close();
            $db->close();
            
            
            $this->disconnect();
            return true;
        }
        return false;
    }
    

    public function update($login, $password, $email, $firstname, $lastname) {
        if ($this->id) {
           
            $db = new mysqli('localhost', 'root', '', 'classes');
            
            
            if ($db->connect_error) {
                die("Erreur de connexion : " . $db->connect_error);
            }
            
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            
            $stmt = $db->prepare("UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $login, $hashedPassword, $email, $firstname, $lastname, $this->id);
            $stmt->execute();
            
           
            $stmt->close();
            $db->close();
            
            
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



echo "<h1>🧪 Tests de la classe User (MySQLi)</h1>";
echo "<hr>";


echo "<h2>📝 Test 1 : Inscription (register)</h2>";
$user1 = new User();
try {
    $result = $user1->register('john_doe', 'motdepasse123', 'john@example.com', 'John', 'Doe');
    echo "✅ Utilisateur inscrit avec succès !<br>";
    echo "🆔 ID: " . $result['id'] . "<br>";
    echo "👤 Login: " . $result['login'] . "<br>";
    echo "📧 Email: " . $result['email'] . "<br>";
    echo "👨 Prénom: " . $result['firstname'] . "<br>";
    echo "👨 Nom: " . $result['lastname'] . "<br>";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}
echo "<hr>";


echo "<h2>🔐 Test 2 : Connexion (connect)</h2>";
$user2 = new User();
if ($user2->connect('john_doe', 'motdepasse123')) {
    echo "✅ Connexion réussie !<br>";
    echo "🆔 ID: " . $user2->getId() . "<br>";
    echo "👤 Login: " . $user2->login . "<br>";
    echo "📧 Email: " . $user2->email . "<br>";
    echo "👨 Prénom: " . $user2->firstname . "<br>";
    echo "👨 Nom: " . $user2->lastname . "<br>";
} else {
    echo "❌ Échec de connexion !<br>";
}
echo "<hr>";


echo "<h2>🔍 Test 3 : Vérifier connexion (isConnected)</h2>";
if ($user2->isConnected()) {
    echo "✅ L'utilisateur est bien connecté !<br>";
} else {
    echo "❌ L'utilisateur n'est pas connecté !<br>";
}
echo "<hr>";


echo "<h2>📋 Test 4 : Récupérer toutes les infos (getAllInfos)</h2>";
$infos = $user2->getAllInfos();
echo "<pre>";
print_r($infos);
echo "</pre>";
echo "<hr>";


echo "<h2>🔎 Test 5 : Getters individuels</h2>";
echo "👤 Login: " . $user2->getLogin() . "<br>";
echo "📧 Email: " . $user2->getEmail() . "<br>";
echo "👨 Prénom: " . $user2->getFirstname() . "<br>";
echo "👨 Nom: " . $user2->getLastname() . "<br>";
echo "<hr>";


echo "<h2>✏️ Test 6 : Mise à jour (update)</h2>";
if ($user2->update('john_doe_updated', 'nouveaumotdepasse', 'john.updated@example.com', 'Johnny', 'Doe Jr.')) {
    echo "✅ Utilisateur mis à jour avec succès !<br>";
    echo "👤 Nouveau login: " . $user2->login . "<br>";
    echo "📧 Nouvel email: " . $user2->email . "<br>";
    echo "👨 Nouveau prénom: " . $user2->firstname . "<br>";
    echo "👨 Nouveau nom: " . $user2->lastname . "<br>";
} else {
    echo "❌ Échec de la mise à jour !<br>";
}
echo "<hr>";


echo "<h2>📝 Test 7 : Inscription d'un 2ème utilisateur</h2>";
$user3 = new User();
try {
    $result2 = $user3->register('jane_smith', 'password456', 'jane@example.com', 'Jane', 'Smith');
    echo "✅ Deuxième utilisateur inscrit avec succès !<br>";
    echo "🆔 ID: " . $result2['id'] . "<br>";
    echo "👤 Login: " . $result2['login'] . "<br>";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}
echo "<hr>";


echo "<h2>🚪 Test 8 : Déconnexion (disconnect)</h2>";
$user2->disconnect();
if (!$user2->isConnected()) {
    echo "✅ Utilisateur déconnecté avec succès !<br>";
    echo "👤 Login après déconnexion: '" . $user2->login . "' (vide attendu)<br>";
} else {
    echo "❌ L'utilisateur est toujours connecté !<br>";
}
echo "<hr>";


echo "<h2>🗑️ Test 9 : Suppression (delete)</h2>";
$user4 = new User();
if ($user4->connect('jane_smith', 'password456')) {
    echo "✅ Connexion à jane_smith réussie !<br>";
    $userId = $user4->getId();
    echo "🆔 ID avant suppression: " . $userId . "<br>";
    
    if ($user4->delete()) {
        echo "✅ Utilisateur supprimé avec succès !<br>";
        echo "👤 Login après suppression: '" . $user4->login . "' (vide attendu)<br>";
        echo "🔍 Connecté après suppression: " . ($user4->isConnected() ? "Oui" : "Non") . "<br>";
    } else {
        echo "❌ Échec de la suppression !<br>";
    }
} else {
    echo "❌ Échec de connexion à jane_smith !<br>";
}
echo "<hr>";


echo "<h2>💾 Test 10 : Vérification dans la base de données</h2>";
$db = new mysqli('localhost', 'root', '', 'classes');
if (!$db->connect_error) {
    $result = $db->query("SELECT * FROM utilisateurs");
    echo "📊 Nombre total d'utilisateurs dans la base: " . $result->num_rows . "<br><br>";
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background-color: #4CAF50; color: white;'>";
    echo "<th>ID</th><th>Login</th><th>Email</th><th>Prénom</th><th>Nom</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['login'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['firstname'] . "</td>";
        echo "<td>" . $row['lastname'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $db->close();
} else {
    echo "❌ Erreur de connexion à la base de données<br>";
}
echo "<hr>";

echo "<h2>✅ Tous les tests sont terminés !</h2>";

?>