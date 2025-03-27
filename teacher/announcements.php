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

// Fonctionnalité pour ajouter une annonce
if(isset($_POST['add_announcement'])){
   $title = $_POST['title'];
   $content = $_POST['content'];
   $status = $_POST['status'];
   
   // Insérer la nouvelle annonce dans la base de données
   $add_announcement = $conn->prepare("INSERT INTO `Announcements` (tutor_id, title, content, status) VALUES (?, ?, ?, ?)");
   $add_announcement->execute([$tutor_id, $title, $content, $status]);
   $message[] = 'Announcement added successfully!';
}

// Fonctionnalité pour supprimer une annonce
if(isset($_POST['delete_announcement'])){
   $delete_id = $_POST['announcement_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   // Vérifier si l'annonce existe et appartient au tuteur
   $verify_announcement = $conn->prepare("SELECT * FROM `Announcements` WHERE announcement_id = ? AND tutor_id = ? LIMIT 1");
   $verify_announcement->execute([$delete_id, $tutor_id]);
   if($verify_announcement->rowCount() > 0){
      // Récupérer les données de l'annonce
      $fetch_announcement = $verify_announcement->fetch(PDO::FETCH_ASSOC);
      // Supprimer l'annonce de la base de données
      $delete_announcement = $conn->prepare("DELETE FROM `Announcements` WHERE announcement_id = ?");
      $delete_announcement->execute([$delete_id]);
      $message[] = 'Announcement deleted!';
   }else{
      $message[] = 'Announcement not found or unauthorized!';
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">

    <!-- Lien vers la bibliothèque font-awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Lien vers le fichier CSS personnalisé -->
    <link rel="stylesheet" href="../css/teacher_style.css">

</head>
<body>

<?php include '../components/teacher_header.php'; ?>
   
<section class="contents">

   <!-- Affichage des messages -->
   <?php if(isset($message)): ?>
      <div class="message">
         <?php foreach($message as $msg): ?>
            <p><?php echo $msg; ?></p>
         <?php endforeach; ?>
      </div>
   <?php endif; ?>

   <!-- Formulaire pour ajouter une nouvelle annonce -->
   <section class="contents">

<h1 class="heading">your announcements </h1>

<div class="box-container">

<div class="box" style="text-align: center;">
   <h3 class="title" style="margin-bottom: .5rem;">create new announcement</h3>
   <a href="add_annoucements.php" class="btn">add announcement</a>
</div>

   <!-- Affichage des annonces existantes -->
   <div class="box-container">

      <?php
         // Récupérer et afficher les annonces
         $select_announcements = $conn->prepare("SELECT * FROM `Announcements` WHERE tutor_id = ? ORDER BY created_at DESC");
         $select_announcements->execute([$tutor_id]);
         if($select_announcements->rowCount() > 0){
            while($announcement = $select_announcements->fetch(PDO::FETCH_ASSOC)){ 
               $announcement_id = $announcement['announcement_id'];
      ?>
            <div class="box">
               <div class="flex">
                  <div>
                     <i class="fas fa-dot-circle" style="<?= ($announcement['status'] == 'active') ? 'color: limegreen;' : 'color: red;' ?>"></i>
                     <span style="<?= ($announcement['status'] == 'active') ? 'color: limegreen;' : 'color: red;' ?>"><?= $announcement['status']; ?></span>
                  </div>
                  <div>
                     <i class="fas fa-calendar"></i>
                     <span><?= $announcement['created_at']; ?></span>
                  </div>
               </div>
               <h3 class="title"><?= $announcement['title']; ?></h3>
               <p><?= $announcement['content']; ?></p>
               <form action="" method="post" class="flex-btn">
                  <input type="hidden" name="announcement_id" value="<?= $announcement_id; ?>">
                  <a href="update_announcement.php?get_id=<?= $announcement_id; ?>" class="option-btn">Update</a>
                  <input type="submit" value="Delete" class="delete-btn" onclick="return confirm('Delete this announcement?');" name="delete_announcement">
               </form>
               <a href="view_announcement.php?get_id=<?= $announcement_id; ?>" class="btn">View Announcement</a>
            </div>
         <?php
            }
         }else{
            echo '<p class="empty">no announcements added yet!</p>';
         }
      ?>

   </div>

</section>

<script src="../js/teacher_script.js"></script>

</body>
</html>
