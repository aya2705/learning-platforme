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

if(isset($_POST['submit'])){
     // Prépare et exécute une requête SQL pour obtenir les informations actuelles du tuteur
   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
   $select_tutor->execute([$tutor_id]);
   $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC); // Récupère les données du tuteur sous forme de tableau associatif

   $prev_pass = $fetch_tutor['password']; // Stocke le mot de passe actuel pour une utilisation ultérieure si nécessaire
   $prev_image = $fetch_tutor['image'];    // Stocke l'image actuelle pour une utilisation ultérieure si nécessaire
      // Récupère et nettoie le nom et l'email fournis via le formulaire
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
      // Si le champ nom n'est pas vide, met à jour le nom dans la base de données
   if(!empty($name)){
      $update_name = $conn->prepare("UPDATE `tutors` SET name = ? WHERE id = ?");
      $update_name->execute([$name, $tutor_id]);
      $message[] = 'username updated successfully!'; // Ajoute un message de succès dans le tableau des messages
   }

  
   if(!empty($email)){
       // Prépare et exécute une requête pour vérifier si l'email existe déjà pour un autre identifiant
      $select_email = $conn->prepare("SELECT email FROM `tutors` WHERE id = ? AND email = ?");
      $select_email->execute([$tutor_id, $email]);
      if($select_email->rowCount() > 0){
         $message[] = 'email already taken!';  // Ajoute un message d'erreur si l'email est déjà pris
      }else{
          // Si l'email n'est pas pris, prépare et exécute la mise à jour de l'email dans la base de données
         $update_email = $conn->prepare("UPDATE `tutors` SET email = ? WHERE id = ?");
         $update_email->execute([$email, $tutor_id]);
         $message[] = 'email updated successfully!';
      }
   }

   $image = $_FILES['image']['name'];  // Récupère le nom original du fichier image téléchargé.
   $image = filter_var($image, FILTER_SANITIZE_STRING); // Nettoie le nom du fichier pour éviter des caractères spéciaux ou malveillants.
   $ext = pathinfo($image, PATHINFO_EXTENSION); // Extrait l'extension du fichier image à partir de son nom.
   $rename = unique_id().'.'.$ext;  // Génère un nouveau nom pour le fichier image en utilisant un identifiant unique suivi de l'extension originale.
   $image_size = $_FILES['image']['size'];  // Récupère la taille du fichier image en octets.
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/'.$rename;  // Définit le chemin du dossier où le fichier image renommé sera enregistré de manière permanente.

   if(!empty($image)){
      if($image_size > 2000000){  // Vérifie si la taille de l'image dépasse 2 Mo
         $message[] = 'image size too large!';
      }else{
         // Prépare et exécute une requête pour mettre à jour l'image du tuteur dans la base de données
         $update_image = $conn->prepare("UPDATE `tutors` SET `image` = ? WHERE id = ?");
         $update_image->execute([$rename, $tutor_id]);
         // Déplace le fichier image du répertoire temporaire vers le répertoire final
         move_uploaded_file($image_tmp_name, $image_folder);
          // Vérifie si une ancienne image existe et est différente de la nouvelle image, puis la supprime
         if($prev_image != '' AND $prev_image != $rename){
            unlink('../uploaded_files/'.$prev_image);
         }
         $message[] = 'image updated successfully!';
      }
   }

   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';  // Définit la valeur SHA-1 d'un mot de passe vide.
   $old_pass = sha1($_POST['old_pass']);  // Hash le mot de passe actuel soumis par l'utilisateur avec SHA-1.
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING); // Nettoie la chaîne hashée pour éviter l'injection de script.
   $new_pass = sha1($_POST['new_pass']); // Hash le nouveau mot de passe soumis par l'utilisateur avec SHA-1.
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING); // Nettoie la chaîne hashée du nouveau mot de passe.
   $cpass = sha1($_POST['cpass']);  // Hash le mot de passe de confirmation soumis par l'utilisateur avec SHA-1.
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);  // Nettoie la chaîne hashée du mot de passe de confirmation.

   if($old_pass != $empty_pass){  // Vérifie si l'ancien mot de passe n'est pas vide
      if($old_pass != $prev_pass){ // Vérifie si l'ancien mot de passe ne correspond pas au mot de passe enregistré
         $message[] = 'old password not matched!'; // Ajoute un message d'erreur si l'ancien mot de passe est incorrect
      }elseif($new_pass != $cpass){  // Vérifie si le nouveau mot de passe ne correspond pas au mot de passe de confirmation
         $message[] = 'confirm password not matched!';
      }else{
         if($new_pass != $empty_pass){  // Vérifie si le nouveau mot de passe n'est pas vide
             // Prépare et exécute la mise à jour du mot de passe dans la base de données
            $update_pass = $conn->prepare("UPDATE `tutors` SET password = ? WHERE id = ?");
            $update_pass->execute([$cpass, $tutor_id]);
            $message[] = 'password updated successfully!';
         }else{
            $message[] = 'please enter a new password!';
         }
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Profile</title>
   

   <!-- Importer des fonts de l'internet -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Importer le fichier .css -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container" style="min-height: calc(100vh - 19rem);">
    <!-- formulaire pour que l'utilisateur peut mettre à jour son profil -->
   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>Update profile</h3>
      <div class="flex">
         <div class="col">
            <p>Your name </p>
            <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" maxlength="50"  class="box">
            <p>Your email </p>
            <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" maxlength="50"  class="box">
         </div>
         <div class="col">
            <p>Old password :</p>
            <input type="password" name="old_pass" placeholder="Enter your old password" maxlength="50"  class="box">
            <p>New password :</p>
            <input type="password" name="new_pass" placeholder="Enter your new password" maxlength="50"  class="box">
            <p>Confirm password :</p>
            <input type="password" name="cpass" placeholder="Confirm your new password" maxlength="50"  class="box">
         </div>
      </div>
      <p>update pic :</p>
      <input type="file" name="image" accept="image/*"  class="box">
      <input type="submit" name="submit" value="update now" class="btn">
   </form>

</section>
<!-- importer le script js -->
<script src="../js/admin_script.js"></script>
   
</body>
</html>