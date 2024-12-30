<?php
session_start(); // Démarrer la session

// Inclusion de la connexion à la base de données
require '../includes/db.php'; // Connexion à la base

// Vérifier si l'utilisateur est administrateur
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur';

// Récupération des informations de Laurent Astoul
$stmt = $pdo->prepare('SELECT * FROM users WHERE nom = :nom AND role = :role');
$stmt->execute(['nom' => 'Astoul', 'role' => 'directeur']);
$laurent = $stmt->fetch(PDO::FETCH_ASSOC);

// Mise à jour des informations si un administrateur soumet le formulaire
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $fieldToUpdate = $_POST['field'] ?? null;
    $updatedValue = $_POST['value'] ?? null;

    if ($fieldToUpdate && $updatedValue) {
        $stmtUpdate = $pdo->prepare("UPDATE users SET $fieldToUpdate = :value WHERE id = :id");
        $stmtUpdate->execute(['value' => $updatedValue, 'id' => $laurent['id']]);

        // Rafraîchir les données après la mise à jour
        $laurent[$fieldToUpdate] = $updatedValue;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=block">
    <link rel="stylesheet" href="../assets/styles_contact.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=home" />
    <title>Contact - Laurent Astoul</title>
</head>

<body>
    <div class="container">
        <h2>Contact - Laurent Astoul</h2>

        <!-- Affichage et modification du Nom -->
        <p><strong>Nom :</strong> 
            <?php if ($isAdmin && isset($_POST['edit']) && $_POST['edit'] === 'nom'): ?>
                <form method="post">
                    <input type="text" name="value" value="<?php echo htmlspecialchars($laurent['nom']); ?>">
                    <input type="hidden" name="field" value="nom">
                    <input type="submit" value="Enregistrer">
                </form>
            <?php else: ?>
                <?php echo htmlspecialchars($laurent['nom']); ?>
                <?php if ($isAdmin): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="edit" value="nom">
                        <input type="submit" value="Modifier">
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </p>

        <!-- Affichage et modification du Rôle -->
        <p><strong>Rôle :</strong> 
            <?php if ($isAdmin && isset($_POST['edit']) && $_POST['edit'] === 'role'): ?>
                <form method="post">
                    <input type="text" name="value" value="<?php echo htmlspecialchars($laurent['role']); ?>">
                    <input type="hidden" name="field" value="role">
                    <input type="submit" value="Enregistrer">
                </form>
            <?php else: ?>
                <?php echo htmlspecialchars($laurent['role']); ?>
                <?php if ($isAdmin): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="edit" value="role">
                        <input type="submit" value="Modifier">
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </p>

        <!-- Affichage et modification de l'Email -->
        <p><strong>Email :</strong> 
            <?php if ($isAdmin && isset($_POST['edit']) && $_POST['edit'] === 'mail'): ?>
                <form method="post">
                    <input type="text" name="value" value="<?php echo htmlspecialchars($laurent['mail']); ?>">
                    <input type="hidden" name="field" value="mail">
                    <input type="submit" value="Enregistrer">
                </form>
            <?php else: ?>
                <?php echo htmlspecialchars($laurent['mail']); ?>
                <?php if ($isAdmin): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="edit" value="mail">
                        <input type="submit" value="Modifier">
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </p>

        <a href="page_accueil.php"><span class="material-symbols-outlined">home</span></a>
    </div>
</body>

</html>
