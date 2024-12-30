<?php
session_start();


require '../includes/db.php'; // Connexion à la base
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur';
$isProf = isset($_SESSION['role']) && $_SESSION['role'] === 'professeur';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet_db', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction pour calculer la moyenne des notes
function calculerMoyenne($notes) {
    if (count($notes) === 0) {
        return null;
    }
    $somme = array_sum(array_column($notes, 'note'));
    return $somme / count($notes);
}

// Récupérer les matières et leurs notes pour un utilisateur spécifique (liées à user_id)
$query = "
    SELECT m.id AS matiere_id, m.nom AS matiere_nom, n.id AS note_id, n.note, n.date
    FROM matieres m
    LEFT JOIN notes n ON m.id = n.matiere_id
    WHERE n.user_id = :user_id
    ORDER BY m.nom, n.date
";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $_SESSION['id']]); // Assurez-vous que l'id de l'utilisateur est stocké dans la session
$matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organiser les données par matière
$matieresData = [];
foreach ($matieres as $row) {
    $matiereId = $row['matiere_id'];
    if (!isset($matieresData[$matiereId])) {
        $matieresData[$matiereId] = [
            'nom' => $row['matiere_nom'],
            'notes' => []
        ];
    }
    if ($row['note_id']) {
        $matieresData[$matiereId]['notes'][] = [
            'id' => $row['note_id'],
            'note' => $row['note'],
            'date' => $row['date']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/styles_note.css" rel="stylesheet">
    <title>Gestion des Notes et Matières</title>
</head>
<body>
    <div class="container">
        <nav>
            <a href="page_accueil.php" style="text-align: center">Retour</a>
        </nav>
        <h1>Gestion des Notes et Matières</h1>

        <!-- Formulaire d'ajout de matière -->
        <?php if ($isAdmin): ?>
            <h2>Ajouter une Matière</h2>
            <form method="POST" action="note.php">
                <label for="nom_matiere">Nom de la matière :</label>
                <input type="text" id="nom_matiere" name="nom_matiere" required>
                <button type="submit" name="action" value="ajouter_matiere">Ajouter</button>
            </form>
        <?php endif; ?>

        <!-- Liste des matières et notes -->
        <div id="list-notes">
            <?php foreach ($matieresData as $matiereId => $matiere): ?>
                <div class="matiere">
                    <h3><?php echo htmlspecialchars($matiere['nom']); ?></h3>
                    <p>Moyenne : <?php echo calculerMoyenne($matiere['notes']) ?? 'N/A'; ?> / 20</p>

                    <!-- Formulaire d'ajout de note -->
                    <?php if ($isAdmin || $isProf): ?>
                        <form method="POST" action="note.php">
                            <input type="hidden" name="matiere_id" value="<?php echo $matiereId; ?>">
                            <label for="note">Note :</label>
                            <input type="number" step="0.01" name="note" required>
                            <label for="date">Date :</label>
                            <input type="date" name="date" required>
                            <button type="submit" name="action" value="ajouter_note">Ajouter Note</button>
                        </form>
                    <?php endif; ?>

                    <!-- Liste des notes -->
                    <ul>
                        <?php foreach ($matiere['notes'] as $note): ?>
                            <li>
                                <?php echo htmlspecialchars($note['date']); ?> : <?php echo htmlspecialchars($note['note']); ?> / 20
                                <!-- Formulaire de suppression de note -->
                                <?php if ($isAdmin): ?>
                                    <form method="POST" action="note.php" style="display:inline;">
                                        <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                                        <button type="submit" name="action" value="supprimer_note">Supprimer</button>
                                    </form>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Formulaire de suppression de matière -->
                    <?php if ($isAdmin): ?>
                        <form method="POST" action="note.php">
                            <input type="hidden" name="matiere_id" value="<?php echo $matiereId; ?>">
                            <button type="submit" name="action" value="supprimer_matiere">Supprimer Matière</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

<?php
// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'ajouter_matiere' && $isAdmin) {
        $nomMatiere = $_POST['nom_matiere'];
        $query = "INSERT INTO matieres (nom) VALUES (:nom)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['nom' => $nomMatiere]);
    } elseif ($action === 'ajouter_note' && ($isAdmin || $isProf)) {
        $matiereId = $_POST['matiere_id'];
        $note = $_POST['note'];
        $date = $_POST['date'];
        $userId = $_SESSION['id']; // Utilisateur connecté
        $query = "INSERT INTO notes (matiere_id, note, date, user_id) VALUES (:matiere_id, :note, :date, :user_id)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'matiere_id' => $matiereId,
            'note' => $note,
            'date' => $date,
            'user_id' => $userId
        ]);
    } elseif ($action === 'supprimer_note' && $isAdmin) {
        $noteId = $_POST['note_id'];
        $query = "DELETE FROM notes WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $noteId]);
    } elseif ($action === 'supprimer_matiere' && $isAdmin) {
        $matiereId = $_POST['matiere_id'];
        $query = "DELETE FROM matieres WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $matiereId]);
    }

    // Recharger la page pour refléter les modifications
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}
?>
