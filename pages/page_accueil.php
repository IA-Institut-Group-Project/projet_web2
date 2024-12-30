<?php 
session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../assets/logo.png" type="png">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=person"/>
        <link href="../assets/styles_accueil.css" rel="stylesheet">
        <!-- Material Icons -->

    <body>
        <!-- Listes pour menu déroulant avec modification balise via fichier css  -->  
        <h1>Bonjour et bienvenu,  <?php echo htmlspecialchars($_SESSION['username']); ?> ! </h1>
        <p>Que voulez-vous faire ? </p>
        <div class="text-background">
            
            <nav>
                <p>
                <a href="page_profile.php">Profile</span></a>
                <br>
                <a href="note.php">Notes</a>
                <br>
                <a href="edt.php">Emplois du temps</a>
                <br>
                <a href="contact.php">Contacter</a>
                <br>
                <a href="add_user.php">Gestion d'utilisateurs</a>
                <br>
                <a href="logout.php">Déconnexion</a>
                </p>
            </nav>
        </div>
        