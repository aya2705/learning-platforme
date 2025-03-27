<?php

// Inclure le fichier de connexion à la base de données
include '../components/connect.php';

// Vérifier si le cookie 'tutor_id' est défini
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   // Rediriger vers la page de connexion si le cookie n'est pas défini
   header('location:login.php');
}

// Vérifier si 'get_id' est passé dans l'URL
if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   // Rediriger vers la page des annonces si 'get_id' n'est pas fourni
   header('location:announcements.php');
}

// Si le formulaire de suppression est soumis
if(isset($_POST['delete_announcement'])){

   // Récupérer et filtrer l'ID de l'annonce à supprimer
   $delete_id = $_POST['announcement_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Supprimer l'annonce de la base de données
   $delete_announcement = $conn->prepare("DELETE FROM `Announcements` WHERE announcement_id = ?");
   $delete_announcement->execute([$delete_id]);
   
   // Rediriger vers la page des annonces après suppression
   header('location:announcements.php');
    
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>View Announcement</title>

   <!-- Lien CDN pour Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien vers le fichier CSS personnalisé -->
   <link rel="stylesheet" href="../css/teacher_style.css">
   <style>
      /* Styles CSS supplémentaires pour centrer et agrandir la partie d'affichage */
      .container {
         display: flex;
         flex-direction: column;
         align-items: center;
      }
   </style>
</head>
<body>

<?php include '../components/teacher_header.php'; ?>

<section class="view-announcement">

   <?php
      // Sélectionner l'annonce à afficher
      $select_announcement = $conn->prepare("SELECT * FROM `Announcements` WHERE announcement_id = ? AND tutor_id = ?");
      $select_announcement->execute([$get_id, $tutor_id]);
      
      // Vérifier si des annonces ont été trouvées
      if($select_announcement->rowCount() > 0){
         while($fetch_announcement = $select_announcement->fetch(PDO::FETCH_ASSOC)){
            $announcement_id = $fetch_announcement['announcement_id'];
   ?>
   <div class="container">
      <h2><?= $fetch_announcement['title']; ?></h2>
      <p><?= $fetch_announcement['content']; ?></p>
      <p>Status: <?= $fetch_announcement['status']; ?></p>
      <form action="" method="post">
         <input type="hidden" name="announcement_id" value="<?= $announcement_id; ?>">
         <input type="submit" value="Delete Announcement" name="delete_announcement" class="delete-btn" onclick="return confirm('Delete this announcement?');">
      </form>
   </div>
   <?php
         }
      }else{
         // Afficher un message si aucune annonce n'est trouvée
         echo '<p class="empty">Announcement not found!</p>';
      }
   ?>

</section>

</body>
</html>
