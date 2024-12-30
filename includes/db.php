<?php
$host = 'localhost'; // Serveur
$dbname = 'projet_db'; // Nom de la base
$username = 'root'; // Nom d'utilisateur (par défaut)
$password = ''; // Mot de passe (par défaut vide dans XAMPP/WAMP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
echo $password
?>
