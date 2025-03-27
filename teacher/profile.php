<?php

   // Inclusion du fichier de connexion à la base de données
   include '../components/connect.php';

   // Vérification de l'existence du cookie tutor_id pour s'assurer que l'utilisateur est connecté
   if(isset($_COOKIE['tutor_id'])){
      $tutor_id = $_COOKIE['tutor_id'];
   }else{
      $tutor_id = '';
      header('location:login.php'); // Redirection vers la page de connexion si le cookie n'existe pas
   }

   // Sélection de toutes les playlists associées à l'ID du tuteur
   $request_id= unique_id();
   $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
   $select_playlists->execute([$tutor_id]);
   $total_playlists = $select_playlists->rowCount(); // Comptage du nombre total de playlists

   // Sélection de tous les contenus associés à l'ID du tuteur
   $select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
   $select_contents->execute([$tutor_id]);
   $total_contents = $select_contents->rowCount(); // Comptage du nombre total de contenus

   // Sélection de tous les commentaires associés à l'ID du tuteur
   $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
   $select_comments->execute([$tutor_id]);
   $total_comments = $select_comments->rowCount(); // Comptage du nombre total de commentaires

   $message = '';

   // Vérification si le formulaire de suppression du compte tuteur a été soumis
   if(isset($_POST['delete_teacher'])) {
       // Insertion d'une nouvelle demande de suppression dans la base de données
       $insert_request = $conn->prepare("INSERT INTO deletion_tutors (request_id, tutor_id) VALUES (?,?)");
       $insert_request->execute([$request_id, $tutor_id]);
       
       // Message de confirmation de la demande de suppression
       $message = "Your deletion request has been sent to the administrator for review";
   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Profile</title>
   
   <!-- Lien CDN pour les icônes Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien vers le fichier CSS personnalisé -->
   <link rel="stylesheet" href="../css/teacher_style.css">

</head>
<body>

<?php include '../components/teacher_header.php'; ?>
   
<section class="tutor-profile" style="min-height: calc(100vh - 19rem);"> 

   <h1 class="heading">Profile details</h1>

   <div class="details">
      <div class="tutor">
         <!-- Affichage de l'image de profil du tuteur -->
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <!-- Affichage du nom du tuteur -->
         <h3><?= $fetch_profile['name']; ?></h3>
         <span>Teacher</span>
         <!-- Lien pour la mise à jour du profil -->
         <a href="update.php" class="inline-btn">Update profile</a>
         <!-- Formulaire pour la suppression du compte tuteur -->
         <form action="profile.php" method="post">
            <button type="submit" name="delete_teacher" class="teacher-btn">Delete My account</button>
         </form>
         <!-- Affichage du message de confirmation s'il existe -->
         <?php if(!empty($message)): ?>
        <p><?php echo $message; ?></p>
        <?php endif; ?>
      </div>
      <div class="flex">
         <div class="box">
            <!-- Affichage du nombre total de playlists -->
            <span><?= $total_playlists; ?></span>
            <p>total playlists</p>
            <a href="playlists.php" class="btn">View playlists</a>
         </div>
         <div class="box">
            <!-- Affichage du nombre total de contenus -->
            <span><?= $total_contents; ?></span>
            <p>total videos</p>
            <a href="contents.php" class="btn">View contents</a>
         </div>
         <div class="box">
            <!-- Affichage du nombre total de commentaires -->
            <span><?= $total_comments; ?></span>
            <p>total comments</p>
            <a href="comments.php" class="btn">View comments</a>
         </div>
      </div>
   </div>

</section>

<script src="../js/teacher_script.js"></script>

</body>
</html>
