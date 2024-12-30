<?php
session_start(); // Démarre la session

// Inclusion de la connexion à la base de données
require '../includes/db.php'; // Connexion à la base

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les informations du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Préparation de la requête pour vérifier l'utilisateur dans la base
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification si l'utilisateur existe et si le mot de passe est correct
    if ($user && $password === $user['password']) {
        // Si les identifiants sont corrects, démarrer la session
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['mail'] = $user['mail'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['id'] = $user['id']; 
        $_SESSION['role'] = $user['role'];

        // Récupérer les événements de l'emploi du temps pour l'utilisateur connecté
        $stmt_emploi = $pdo->prepare('SELECT * FROM emplois');
        $stmt_emploi->execute();
        $emplois = $stmt_emploi->fetchAll(PDO::FETCH_ASSOC);

        // Stocker les événements dans la session pour l'affichage sur la page d'accueil
        $_SESSION['emplois'] = $emplois;

        // Redirection vers la page d'accueil
        header('Location: page_accueil.php');
        exit;
    } else {
        // Si l'authentification échoue
        $error_message = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/styles_login.css" rel="stylesheet">
    <title>Connexion</title>
</head>
<body>
    <div class="form-signin">
        <h1 class="bd-placeholder-img-lg">Connexion</h1>
        <?php if (isset($error_message)) : ?>
            <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-floating">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required class="form-control">
            </div>
            <div class="form-floating">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required class="form-control">
            </div>
            <button class="btn-bd-primary" type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>

