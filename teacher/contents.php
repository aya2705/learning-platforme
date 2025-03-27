<?php

// Inclusion du fichier de connexion à la base de données
include '../components/connect.php';

// Vérification de l'existence du cookie 'tutor_id'
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   // Redirection vers la page de connexion si le cookie n'est pas défini
   header('location:login.php');
}

// Vérification de la soumission du formulaire de suppression de vidéo
if(isset($_POST['delete_video'])){
   $delete_id = $_POST['video_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING); // Nettoyage de l'ID de la vidéo
   // Vérification de l'existence de la vidéo dans la base de données
   $verify_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
   $verify_video->execute([$delete_id]);
   if($verify_video->rowCount() > 0){
      // Sélection et suppression de la miniature de la vidéo
      $delete_video_thumb = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_video_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_video_thumb->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_thumb['thumb']);
      // Sélection et suppression de la vidéo elle-même
      $delete_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_video->execute([$delete_id]);
      $fetch_video = $delete_video->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_video['video']);
      // Suppression des commentaires associés à la vidéo
      $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
      $delete_comments->execute([$delete_id]);
      // Suppression du contenu de la vidéo
      $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
      $delete_content->execute([$delete_id]);
      $message[] = 'video deleted!'; // Message de succès
   }else{
      $message[] = 'video already deleted!'; // Message d'erreur si la vidéo est déjà supprimée
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- Lien CDN pour Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien vers le fichier CSS personnalisé -->
   <link rel="stylesheet" href="../css/teacher_style.css">

</head>
<body>

<!-- Inclusion de l'en-tête du professeur -->
<?php include '../components/teacher_header.php'; ?>
   
<section class="contents">

   <h1 class="heading">your contents</h1>

   <div class="box-container">

   <div class="box" style="text-align: center;">
      <h3 class="title" style="margin-bottom: .5rem;">create new content</h3>
      <!-- Lien pour ajouter un nouveau contenu -->
      <a href="add_content.php" class="btn">add content</a>
   </div>

   <?php
      // Sélection des vidéos associées au tuteur connecté
      $select_videos = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ? ORDER BY date DESC");
      $select_videos->execute([$tutor_id]);
      if($select_videos->rowCount() > 0){
         while($fecth_videos = $select_videos->fetch(PDO::FETCH_ASSOC)){ 
            $video_id = $fecth_videos['id'];
   ?>
      <div class="box">
         <div class="flex">
            <!-- Affichage du statut de la vidéo avec couleur correspondante -->
            <div><i class="fas fa-dot-circle" style="<?php if($fecth_videos['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fecth_videos['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fecth_videos['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fecth_videos['date']; ?></span></div>
         </div>
         <!-- Affichage de la miniature ou message si elle est absente -->
         <?php if(empty($fecth_videos['thumb']) || empty($fecth_videos['video'])): ?>
            <p class="big-font"><?php if(empty($fecth_videos['thumb'])){ echo 'tap to see video'; } else { echo 'text'; } ?></p>
         <?php else: ?>
            <img src="../uploaded_files/<?= $fecth_videos['thumb']; ?>" class="thumb" alt="">
         <?php endif; ?>
         <h3 class="title"><?= $fecth_videos['title']; ?></h3>
         <!-- Formulaire pour mettre à jour ou supprimer le contenu -->
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="video_id" value="<?= $video_id; ?>">
            <a href="update_content.php?get_id=<?= $video_id; ?>" class="option-btn">update</a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this video?');" name="delete_video">
         </form>
         <!-- Lien pour voir le contenu -->
         <a href="view_content.php?get_id=<?= $video_id; ?>" class="btn">view content</a>
      </div>
   <?php
         }
      }else{
         echo '<p class="empty">no contents added yet!</p>'; // Message si aucun contenu n'est trouvé
      }
   ?>

   </div>

</section>

<!-- Inclusion du script JavaScript -->
<script src="../js/teacher_script.js"></script>

</body>
</html>
