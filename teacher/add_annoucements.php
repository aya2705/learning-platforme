<?php
// Inclusion du fichier de connexion à la base de données
include '../components/connect.php';

// Vérification si le cookie tutor_id est défini, sinon rediriger vers la page de connexion
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
} else {
   $tutor_id = '';
   header('location:login.php');
   exit(); // Assurez-vous que le script s'arrête après la redirection
}

// Fonctionnalité pour ajouter une annonce
if(isset($_POST['add_announcement'])){
    $title = $_POST['title'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    
    // Insertion de la nouvelle annonce dans la base de données
    $add_announcement = $conn->prepare("INSERT INTO `Announcements` (tutor_id, title, content, status) VALUES (?, ?, ?, ?)");
    $add_announcement->execute([$tutor_id, $title, $content, $status]);
    $message[] = 'Annonce ajoutée avec succès!';
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>

    <!-- Lien CDN pour Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Lien du fichier CSS personnalisé -->
    <link rel="stylesheet" href="../css/teacher_style.css">

</head>

<body>

    <?php include '../components/teacher_header.php'; ?>

    <section class="video-form">

        <h1 class="heading">Ajouter une annonce</h1>

        <!-- Formulaire pour ajouter une nouvelle annonce -->
        <form action="" method="post">
            <!-- Liste déroulante pour le statut de l'annonce -->
            <p>Statut de l'annonce <span>*</span></p>
            <select name="status" class="box" required>
                <option value="" selected disabled>Statut</option>
                <option value="active">Active</option>
                <option value="deactive">Désactive</option>
            </select>

            <!-- Champ de saisie pour le titre de l'annonce -->
            <p>Titre de l'annonce <span>*</span></p>
            <input type="text" name="title" required placeholder="Entrez le titre de l'annonce" class="box">

            <!-- Zone de texte pour le contenu de l'annonce -->
            <p>Contenu de l'annonce <span>*</span></p>
            <textarea name="content" required placeholder="Rédigez le contenu de l'annonce" class="box" cols="30" rows="10"></textarea>

            <!-- Bouton de soumission pour ajouter l'annonce -->
            <input type="submit" value="Ajouter l'annonce" name="add_announcement" class="btn">
        </form>

    </section>

    <!-- Fichier JavaScript pour des fonctionnalités supplémentaires -->
    <script src="../js/teacher_script.js"></script>

</body>

</html>
