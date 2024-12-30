<?php
session_start();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur';
$isProf = isset($_SESSION['role']) && $_SESSION['role'] === 'professeur';

// Connexion à la base de données
$host = 'localhost';
$dbname = 'projet_db';
$username = 'root'; // Remplacez par votre utilisateur MySQL
$password = ''; // Remplacez par votre mot de passe MySQL
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
}

// Récupérer les événements de l'emploi du temps
$query = "SELECT * FROM emplois";
$stmt = $pdo->prepare($query);
$stmt->execute();
$emploiDuTemps = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mettre à jour un événement si une modification est soumise
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($isProf || $isAdmin)) {
    $id = $_POST['id'];
    $heure = $_POST['heure'];
    $titre = $_POST['titre'];

    $updateQuery = "UPDATE emplois SET heure = :heure, titre = :titre WHERE id = :id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute(['heure' => $heure, 'titre' => $titre, 'id' => $id]);

    // Recharger la page pour refléter les modifications
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/styles_edt.css" rel="stylesheet">
    <link rel="icon" href="../assets/logo.png" type="png">
    <title>Emploi du Temps - Aujourd'hui</title>
</head>
<body>
    <div class="text-background">
        <h1>Emploi du Temps du <p id="datetime"></p></h1>
        <div id="emploi-du-temps" class="emploi-du-temps">
            <?php foreach ($emploiDuTemps as $event): ?>
                <div class="event">
                    <?php if ($isAdmin || $isProf): ?>
                        <!-- Formulaire pour modifier les données -->
                        <form method="POST" action="">
                            <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                            <input type="text" name="heure" value="<?php echo $event['heure']; ?>" required>
                            <input type="text" name="titre" value="<?php echo $event['titre']; ?>" required>
                            <button type="submit">Enregistrer</button>
                        </form>
                    <?php else: ?>
                        <!-- Affichage simple si non-admin -->
                        <span class="heure"><?php echo $event['heure']; ?></span>
                        <span class="activite"><?php echo $event['titre']; ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <nav>
            <a href="page_accueil.php" style="text-align: center">Retour</a>
        </nav>
    </div>

    <script>
        var nowDate = new Date(); 
        var date = nowDate.getDate() + '/' + (nowDate.getMonth() + 1) + '/' + nowDate.getFullYear(); 
        var datetime = date.toLocaleString();
        document.getElementById("datetime").innerHTML = datetime;
    </script>
</body>
</html>
