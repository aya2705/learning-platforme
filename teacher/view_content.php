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
   // Rediriger vers la page des contenus si 'get_id' n'est pas fourni
   header('location:contents.php');
}

// Si le formulaire de suppression de vidéo est soumis
if(isset($_POST['delete_video'])){

   // Récupérer et filtrer l'ID de la vidéo à supprimer
   $delete_id = $_POST['video_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Supprimer la miniature de la vidéo du serveur
   $delete_video_thumb = $conn->prepare("SELECT thumb FROM `content` WHERE id = ? LIMIT 1");
   $delete_video_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_video_thumb->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/'.$fetch_thumb['thumb']);

   // Supprimer la vidéo du serveur
   $delete_video = $conn->prepare("SELECT video FROM `content` WHERE id = ? LIMIT 1");
   $delete_video->execute([$delete_id]);
   $fetch_video = $delete_video->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/'.$fetch_video['video']);

  
   // Supprimer les commentaires associés à la vidéo
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
   $delete_comments->execute([$delete_id]);

   // Supprimer la vidéo de la base de données
   $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
   $delete_content->execute([$delete_id]);
   
   // Rediriger vers la page des contenus après suppression
   header('location:contents.php');
}

// Si le formulaire de suppression de commentaire est soumis
if(isset($_POST['delete_comment'])){

   // Récupérer et filtrer l'ID du commentaire à supprimer
   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Vérifier si le commentaire existe
   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   if($verify_comment->rowCount() > 0){
      // Supprimer le commentaire de la base de données
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      $message[] = 'comment deleted successfully!';
   }else{
      $message[] = 'comment already deleted!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>view content</title>

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
      .pdf-viewer, .image-viewer, .video {
         width: 100%; /* Ajuster la largeur selon les besoins */
         height: 100%; /* Limiter la hauteur maximale */
      }
      .no-file-description {
         font-size: 20px;
         font-weight: bold;
         text-align: center;
         margin-top: 20px;
      }
   </style>
</head>
<body>

<?php include '../components/teacher_header.php'; ?>


<section class="view-content">

   <?php
      // Sélectionner le contenu à afficher
      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND tutor_id = ?");
      $select_content->execute([$get_id, $tutor_id]);
      
      // Vérifier si des contenus ont été trouvés
      if($select_content->rowCount() > 0){
         while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
            $video_id = $fetch_content['id'];


            // Compter le nombre de commentaires pour la vidéo

            $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ? AND content_id = ?");
            $count_comments->execute([$tutor_id, $video_id]);
            $total_comments = $count_comments->rowCount();
   ?>
   <div class="container">
   <?php
   $video_path = "../uploaded_files/" . $fetch_content['video'];
   $video_extension = strtolower(pathinfo($video_path, PATHINFO_EXTENSION));
   ?>
   <?php if ($video_extension !== '') : ?>
    <?php if ($video_extension === 'pdf') : ?>
        <embed src="<?= $video_path ?>" type="application/pdf" class="pdf-viewer">
        <p class="file-type">PDF</p>
    <?php elseif ($video_extension === 'pptx') : ?>
        <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($video_path) ?>" frameborder="0"></iframe>
        <p class="file-type">PowerPoint</p>
    <?php elseif (in_array($video_extension, ['jpg', 'jpeg', 'png', 'gif'])) : ?>
        <img src="<?= $video_path ?>" alt="Image" class="image-viewer">
        <p class="file-type">Image</p>
    <?php else : ?>
        <video src="<?= $video_path ?>" autoplay controls poster="../uploaded_files/<?= $fetch_content['thumb']; ?>" class="video"></video>
        <p class="file-type">Video</p>
    <?php endif; ?>
   <?php endif; ?>

   <?php if (!empty($fetch_content['description'])) : ?>
       <div class="description"><?= $fetch_content['description']; ?></div>
   <?php endif; ?>

   <div class="date"><i class="fas fa-calendar"></i><span><?= $fetch_content['date']; ?></span></div>
   <h3 class="title"><?= $fetch_content['title']; ?></h3>
   <div class="flex">
      <div><i class="fas fa-comment"></i><span><?= $total_comments; ?></span></div>
   </div>
   <form action="" method="post">
      <div class="flex-btn">
         <input type="hidden" name="video_id" value="<?= $video_id; ?>">
         <a href="update_content.php?get_id=<?= $video_id; ?>" class="option-btn">update</a>
         <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this video?');" name="delete_video">
      </div>
   </form>
</div>

   <?php
    }
   }else{
      // Message affiché si aucun contenu n'est trouvé
      echo '<p class="empty">no contents added yet! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">add videos</a></p>';
   }
      
   ?>

</section>

<section class="comments">

   <h1 class="heading">user comments</h1>

   
   <div class="show-comments">
      <?php
         // Sélectionner les commentaires associés au contenu
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ?");
         $select_comments->execute([$get_id]);
         
         // Vérifier si des commentaires ont été trouvés
         if($select_comments->rowCount() > 0){
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){   
               $select_commentor = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_commentor->execute([$fetch_comment['user_id']]);
               $fetch_commentor = $select_commentor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="user">
            <img src="../uploaded_files/<?= $fetch_commentor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_commentor['name']; ?></h3>
               <span><?= $fetch_comment['date']; ?></span>
            </div>
         </div>
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('delete this comment?');">delete comment</button>
         </form>
      </div>
      <?php
       }
      }else{
         // Message affiché si aucun commentaire n'est trouvé
         echo '<p class="empty">no comments added yet!</p>';
      }
      ?>
      </div>
   
</section>

<script src="../js/teacher_script.js"></script>

</body>
</html>
