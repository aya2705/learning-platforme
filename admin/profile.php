<?php

   include '../components/connect.php';
    //La vérification de l'existence du cookie tutor_id permet de déterminer si l'utilisateur a été précédemment
    // identifié en tant que tuteur ou s'il doit être redirigé vers la page de connexion.
    // Vérifie si le cookie tutor_id est défini
   if(isset($_COOKIE['tutor_id'])){
      // Si oui, assigne sa valeur à la variable $tutor_id
      $tutor_id = $_COOKIE['tutor_id'];
   }else{
         // Si le cookie tutor_id n'est pas défini, initialise $tutor_id à une chaîne vide
      $tutor_id = '';
         // Redirige l'utilisateur vers la page de connexion
      header('location:login.php');
   }

   
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Profile</title>
   

  <!-- Importer des fonts de l'internet -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

  <!-- Importer le fichier css -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="tutor-profile" style="min-height: calc(100vh - 19rem);"> 

   <h1 class="heading">Profile details</h1>

   <div class="details">
      <div class="tutor">
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt=""> <!-- Affichage de l'image du profil du tuteur -->
         <h3><?= $fetch_profile['name']; ?></h3> <!-- Affichage du nom du tuteur -->
         <span>Teacher</span><!-- Affichage du role du tuteur -->
         <a href="update.php" class="inline-btn">Update profile</a>  <!-- Lien pour mettre à jour le profil -->
      </div>
      
   </div>

</section>

<!-- importer le script js -->
<script src="../js/admin_script.js"></script>

</body>
</html>