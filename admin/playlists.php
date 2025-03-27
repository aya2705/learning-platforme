<?php

include '../components/connect.php';

if(isset($_POST['delete'])){ //Vérification de la soumission du formulaire
   //Nettoyage de l'identifiant de la playlist 
   $delete_id = $_POST['playlist_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
    //Vérification de l'existence de la playlist 
   $verify_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ?");
   $verify_playlist->execute([$delete_id]);

   if($verify_playlist->rowCount() > 0){
      //Suppression des éléments associés à la playlist 
      $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ?");
      $delete_playlist_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_thumb['thumb']);
      $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
      $delete_bookmark->execute([$delete_id]);
      $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
      $delete_playlist->execute([$delete_id]);
      $message[] = 'playlist deleted!';
   }else{
      $message[] = 'playlist already deleted!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Playlists</title>
   

   <!-- Importer des fonts de l'internet -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Importer le fichier css -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="playlists">

   <h1 class="heading">added playlists</h1>

   <div class="box-container">

      <?php
      //Préparation et exécution de la requête SQL pour les playlists
         $select_playlist = $conn->prepare("SELECT * FROM `playlist` ORDER BY date DESC");
         $select_playlist->execute();

         if($select_playlist->rowCount() > 0){//Vérification de l'existence de playlists 
         while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){//Boucle pour traiter chaque playlist
            //Compte des vidéos dans chaque playlist
            $playlist_id = $fetch_playlist['id'];
            $count_videos = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
            $count_videos->execute([$playlist_id]);
            $total_videos = $count_videos->rowCount();
            //Récupération des informations sur le tuteur de la playlist 
            $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
            $select_tutor->execute([$fetch_playlist['tutor_id']]);
            $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
          
         <div class="flex">
            <!-- Statut de la Playlist -->
            <div><i class="fas fa-circle-dot" style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fetch_playlist['status']; ?></span></div>
            <!-- Date de la Playlist  -->
            <div><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         </div>
         <!-- Miniature de la Playlist -->
         <div class="thumb">
            <span><?= $total_videos; ?></span>
            <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
         </div>
         <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
         <p class="description"><?= $fetch_playlist['description']; ?></p>
         <!-- Lien pour Voir la Playlist -->
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="btn">view playlist</a>
      </div>
      <?php
         } 
      }else{
         // Gestion de l'Absence de Playlists
         echo '<p class="empty">no playlist added yet!</p>';
      }
      ?>

   </div>

</section>
 <!-- importer le script js -->
<script src="../js/admin_script.js"></script>

<script>
   document.querySelectorAll('.playlists .box-container .box .description').forEach(content => {
      if(content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
   });
</script>

</body>
</html>
