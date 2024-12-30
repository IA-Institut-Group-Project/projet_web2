<?php
// Démarrer la session pour conserver les données
session_start();

// Inclure la connexion à la BDD

require '../includes/db.php';
if ($pdo) {
    echo "Connexion réussie à la base de données.";
} else {
    echo "Échec de connexion.";
}


// Initialiser les variables si elles ne sont pas définies
if (!isset($_SESSION['username'])) $_SESSION['username'] = '';
if (!isset($_SESSION['nom'])) $_SESSION['nom'] = '';
if (!isset($_SESSION['mail'])) $_SESSION['mail'] = '';

// Variable pour gérer l'état d'édition
if (!isset($_SESSION['editing'])) $_SESSION['editing'] = [];

// Gérer la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['modifier'])) {
        // Activer le mode édition pour le champ sélectionné
        $_SESSION['editing'][$_POST['modifier']] = true;
    } elseif (isset($_POST['annuler'])) {
        // Désactiver le mode édition pour le champ sélectionné
        unset($_SESSION['editing'][$_POST['annuler']]);
    } elseif (isset($_POST['field'])) {
        // Sauvegarder le champ et mettre à jour la BDD
        $field = $_POST['field'];
        $value = trim($_POST[$field] ?? ''); // Récupérer et nettoyer la valeur
        
        
        // Mettre à jour la session
        $_SESSION[$field] = htmlspecialchars($value); // Échapper la valeur
        
        // Préparer la requête SQL pour mettre à jour la BDD
        $stmt = $pdo->prepare("UPDATE users SET $field = :value WHERE username = :username");
        $stmt->execute([
            'value' => $value,
            'username' => $_SESSION['username'], // Identifier l'utilisateur connecté
        ]);

        unset($_SESSION['editing'][$field]); // Quitter le mode édition
    }
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/logo.png" type="png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=block" />
    <link href="../assets/styles_profile.css" rel="stylesheet">
    <title>Donnée personnelles</title>
</head>

<body>
    <div class="text-background">
        <h2>Informations personnelles</h2>

        <nav>
            <!-- Formulaire pour le Nom -->
            <?php if (isset($_SESSION['editing']['username'])): ?>
                <form method="post">
                    <label for="username"><span class="material-symbols-outlined">id_card</span></label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
                    <input type="hidden" name="field" value="username">
                    <input type="submit" value="Enregistrer">
                    <button type="submit" name="annuler" value="username">Annuler</button>
                </form>
            <?php elseif (empty($_SESSION['username'])): ?>
                <form method="post">
                    <label for="username"><span class="material-symbols-outlined">id_card</span></label>
                    <input type="text" id="username" name="username">
                    <input type="hidden" name="field" value="username">
                    <input type="submit" value="Envoyer">
                </form>
            <?php else: ?>
                <p class="output">
                <label for="username"><span class="material-symbols-outlined">id_card</span></label><?php echo htmlspecialchars($_SESSION['username']); ?>
                    <form method="post" style="text-align: center;">
                        <input type="hidden" name="modifier" value="username">
                        <input type="submit" value="Modifier">
                    </form>
                </p>
            <?php endif; ?>

            <!-- Formulaire pour le Prénom -->
            <?php if (isset($_SESSION['editing']['nom'])): ?>
                <form method="post" style="text-align: center;">
                    <label for="nom"><span class="material-symbols-outlined">id_card</span></label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($_SESSION['nom']); ?>">
                    <input type="hidden" name="field" value="nom">
                    <input type="submit" value="Enregistrer">
                    <button type="submit" name="annuler" value="nom">Annuler</button>
                </form>
            <?php elseif (empty($_SESSION['nom'])): ?>
                <form method="post">
                    <label for="nom"><span class="material-symbols-outlined">id_card</span></label>
                    <input type="text" id="nom" name="nom">
                    <input type="hidden" name="field" value="nom">
                    <input type="submit" value="Envoyer">
                </form>
            <?php else: ?>
                <p class="output">
                    <label for="nom"><span class="material-symbols-outlined">id_card</span></label>
                    <?php echo htmlspecialchars($_SESSION['nom']); ?>
                        <form method="post" style="text-align: center;">
                            <input type="hidden" name="modifier" value="nom">
                            <input type="submit" value="Modifier">
                        </form>
                </p>
            <?php endif; ?>

            <!-- Formulaire pour le Mail -->
            <?php if (isset($_SESSION['editing']['mail'])): ?>
                <form method="post">
                    <label for="mail"><span class="material-symbols-outlined">mail</span></label>
                    <input type="text" id="mail" name="mail" value="<?php echo htmlspecialchars($_SESSION['mail']); ?>" pattern=".+@.+">
                    <input type="hidden" name="field" value="mail">
                    <input type="submit" style="text-align: center;" value="Enregistrer">
                    <button type="submit" style="text-align: center;"name="annuler" value="mail">Annuler</button>
                </form>
            <?php elseif (empty($_SESSION['mail'])): ?>
                <form method="post" >
                    <label for="mail"><span class="material-symbols-outlined">mail</span></label>
                    <input type="text" id="mail" name="mail">
                    <input type="hidden" name="field" value="mail">
                    <input type="submit" value="Envoyer">
                </form>
            <?php else: ?>
                <p class="output"><label for="mail"><span class="material-symbols-outlined">mail</span></label>(@ requis) : <?php echo htmlspecialchars($_SESSION['mail']); ?>
                    <form method="post" style="text-align: center;">
                        <input type="hidden" name="modifier" value="mail">
                        <input type="submit" value="Modifier" >
                    </form>
                </p>
            <?php endif; ?>

            <a href="page_accueil.php", style="text-align: center"><span class="material-symbols-outlined">home</span></a>
        </nav>
    </div>
</body>

</html>
