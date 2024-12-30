<?php
// Démarre la session pour accéder aux informations stockées en session
session_start();

// Inclusion du fichier de connexion à la base de données
require '../includes/db.php'; // Connexion à la base de données

// Vérification si l'utilisateur est administrateur
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur';

// Si l'utilisateur n'est pas un administrateur, redirection vers la page d'accueil
if (!$isAdmin) {
    header("Location: page_accueil.php");
    exit;
}

// Tentative de connexion à la base de données avec PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet_db', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur de connexion, affichage du message d'erreur et arrêt du script
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement du formulaire d'ajout d'un élève
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $username = $_POST['username']; // Prénom
    $password = $_POST['password']; // Mot de passe
    $role = $_POST['role'];         // Rôle (élève, professeur, administrateur)
    $nom = $_POST['nom'];           // Nom
    $mail = $_POST['mail'];         // Email

    // Requête SQL pour insérer un nouvel utilisateur dans la table "users"
    $query = "INSERT INTO users (username, password, role, nom, mail) VALUES (:username, :password, :role, :nom, :mail)";
    $stmt = $pdo->prepare($query); // Préparation de la requête SQL
    $stmt->execute([               // Exécution de la requête avec les données du formulaire
        'username' => $username,
        'password' => $password,
        'role' => $role,
        'nom' => $nom,
        'mail' => $mail
    ]);

    // Redirection vers la même page après l'ajout, pour éviter la soumission multiple du formulaire
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Inclusion de la feuille de style pour le formulaire -->
    <link href="../assets/styles_note.css" rel="stylesheet">
    <title>Ajouter un Élève</title>
</head>
<body>
    <div class="container">
        <!-- Navigation permettant de revenir à la page d'accueil -->
        <nav>
            <a href="page_accueil.php" style="text-align: center">Retour</a>
        </nav>
        <h1>Ajouter un Élève</h1>

        <!-- Formulaire pour ajouter un nouvel élève -->
        <form method="POST" action="add_user.php">
            <label for="username">Prénom :</label> <!-- Label pour le champ prénom -->
            <input type="text" id="username" name="username" required> <!-- Champ de saisie pour le prénom -->

            <label for="nom">Nom :</label> <!-- Label pour le champ nom -->
            <input type="text" id="nom" name="nom" required> <!-- Champ de saisie pour le nom -->

            <label for="mail">E-mail :</label> <!-- Label pour le champ email -->
            <input type="email" id="mail" name="mail" required> <!-- Champ de saisie pour l'email -->

            <label for="password">Mot de passe :</label> <!-- Label pour le champ mot de passe -->
            <input type="password" id="password" name="password" required> <!-- Champ de saisie pour le mot de passe -->

            <label for="role">Rôle :</label> <!-- Label pour le champ rôle -->
            <select id="role" name="role" required> <!-- Menu déroulant pour sélectionner le rôle -->
                <option value="eleve">Élève</option> <!-- Option pour le rôle Élève -->
                <option value="professeur">Professeur</option> <!-- Option pour le rôle Professeur -->
                <option value="administrateur">Administrateur</option> <!-- Option pour le rôle Administrateur -->
            </select>

            <!-- Bouton pour soumettre le formulaire -->
            <button type="submit">Ajouter l'élève</button>
        </form>
    </div>
</body>
</html>
