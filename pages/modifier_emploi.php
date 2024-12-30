<?php
session_start();
$host = 'localhost'; // Serveur
$dbname = 'projet_db'; // Nom de la base
$username = 'root'; // Nom d'utilisateur (par défaut)
$password = ''; // Mot de passe (par défaut vide dans XAMPP/WAMP)


// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
}

// Vérification si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $heure = $_POST['heure'];
    $titre = $_POST['titre'];
    $type = $_POST['type'];

    // Mettre à jour l'événement dans la base de données
    $query = "UPDATE emplois SET heure = :heure, titre = :titre, type = :type WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':heure', $heure);
    $stmt->bindParam(':titre', $titre);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Redirection vers la page principale
    header("Location: index.php"); // Ou la page d'accueil
    exit();
}
?>
