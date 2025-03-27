<?php

// Inclusion du fichier de connexion à la base de données
include '../components/connect.php';

// Vérification de l'existence du cookie 'tutor_id'
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   // Redirection vers la page de connexion si le cookie n'existe pas
   $tutor_id = '';
   header('location:login.php');
}

// Vérification de la soumission du formulaire
if(isset($_POST['submit'])){

   // Génération d'un identifiant unique pour la nouvelle playlist
   $id = unique_id();
   $title = $_POST['title'];
   // Filtrage du titre pour supprimer les caractères indésirables
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $description = $_POST['description'];
   // Filtrage de la description pour supprimer les caractères indésirables
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $status = $_POST['status'];
   // Filtrage du statut pour supprimer les caractères indésirables
   $status = filter_var($status, FILTER_SANITIZE_STRING);

   // Gestion du téléchargement de l'image
   $image = $_FILES['image']['name'];
   // Filtrage du nom de l'image pour supprimer les caractères indésirables
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   // Renommage de l'image avec un identifiant unique et son extension
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   // Définition du chemin de destination de l'image téléchargée
   $image_folder = '../uploaded_files/'.$rename;

   // Préparation et exécution de la requête d'insertion de la nouvelle playlist dans la base de données
   $add_playlist = $conn->prepare("INSERT INTO `playlist`(id, tutor_id, title, description, thumb, status) VALUES(?,?,?,?,?,?)");
   $add_playlist->execute([$id, $tutor_id, $title, $description, $rename, $status]);

   // Déplacement de l'image téléchargée vers le dossier spécifié
   move_uploaded_file($image_tmp_name, $image_folder);

   // Ajout d'un message de confirmation de création de la playlist
   $message[] = 'new playlist created!';  

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add new Playlist</title>

    <!-- Lien vers la bibliothèque font-awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Lien vers le fichier CSS personnalisé -->
    <link rel="stylesheet" href="../css/teacher_style.css">

</head>

<body>

    <!-- Inclusion de l'en-tête pour les enseignants -->
    <?php include '../components/teacher_header.php'; ?>

    <section class="playlist-form">

        <h1 class="heading">Create new Playlist</h1>

        <!-- Formulaire pour la création d'une nouvelle playlist -->
        <form action="" method="post" enctype="multipart/form-data">
            <p>playlist status <span>*</span></p>
            <select name="status" class="box" required>
                <option value="" selected disabled>Status</option>
                <option value="active">Active</option>
                <option value="deactive">Desactive</option>
            </select>
            <p>playlist title <span>*</span></p>
            <input type="text" name="title" maxlength="100" required placeholder="enter playlist title" class="box">
            <p>playlist description <span>*</span></p>
            <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30"
                rows="10"></textarea>
            <p>playlist picture <span>*</span></p>
            <input type="file" name="image" accept="image/*" required class="box">
            <input type="submit" value="create playlist" name="submit" class="btn">
        </form>

    </section>

    <!-- Inclusion du fichier JavaScript personnalisé -->
    <script src="../js/teacher_script.js"></script>

</body>

</html>
