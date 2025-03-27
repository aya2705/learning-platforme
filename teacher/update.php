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

// Si le formulaire est soumis
if(isset($_POST['submit'])){

   // Sélectionner le tuteur actuel à partir de la base de données
   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
   $select_tutor->execute([$tutor_id]);
   $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

   // Stocker les valeurs actuelles du mot de passe et de l'image
   $prev_pass = $fetch_tutor['password'];
   $prev_image = $fetch_tutor['image'];

   // Récupérer et filtrer les nouvelles données du formulaire
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   // Mettre à jour le nom si un nouveau nom est fourni
   if(!empty($name)){
      $update_name = $conn->prepare("UPDATE `tutors` SET name = ? WHERE id = ?");
      $update_name->execute([$name, $tutor_id]);
      $message[] = 'username updated successfully!';
   }

   // Mettre à jour l'email si un nouvel email est fourni
   if(!empty($email)){
      $select_email = $conn->prepare("SELECT email FROM `tutors` WHERE id = ? AND email = ?");
      $select_email->execute([$tutor_id, $email]);
      if($select_email->rowCount() > 0){
         $message[] = 'email already taken!';
      }else{
         $update_email = $conn->prepare("UPDATE `tutors` SET email = ? WHERE id = ?");
         $update_email->execute([$email, $tutor_id]);
         $message[] = 'email updated successfully!';
      }
   }

   // Récupérer et traiter la nouvelle image
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/'.$rename;

   // Mettre à jour l'image si une nouvelle image est fournie
   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'image size too large!';
      }else{
         $update_image = $conn->prepare("UPDATE `tutors` SET `image` = ? WHERE id = ?");
         $update_image->execute([$rename, $tutor_id]);
         move_uploaded_file($image_tmp_name, $image_folder);
         if($prev_image != '' AND $prev_image != $rename){
            unlink('../uploaded_files/'.$prev_image);
         }
         $message[] = 'image updated successfully!';
      }
   }

   // Gestion des mots de passe
   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   // Vérifier et mettre à jour le mot de passe
   if($old_pass != $empty_pass){
      if($old_pass != $prev_pass){
         $message[] = 'old password not matched!';
      }elseif($new_pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         if($new_pass != $empty_pass){
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


    <!-- Lien CDN pour Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Lien vers le fichier CSS personnalisé -->
    <link rel="stylesheet" href="../css/teacher_style.css">

</head>

<body>

    <?php include '../components/teacher_header.php'; ?>

    <!-- Section de mise à jour du profil commence -->

    <section class="form-container" style="min-height: calc(100vh - 19rem);">

        <form class="register" action="" method="post" enctype="multipart/form-data">
            <h3>Update profile</h3>
            <div class="flex">
                <div class="col">
                    <p>Your name </p>
                    <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" maxlength="50"
                        class="box">
                    <p>Your email </p>
                    <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" maxlength="20"
                        class="box">
                </div>
                <div class="col">
                    <p>Old password :</p>
                    <input type="password" name="old_pass" placeholder="Enter your old password here" maxlength="20"
                        class="box">
                    <p>New password :</p>
                    <input type="password" name="new_pass" placeholder="Enter your new password here" maxlength="20"
                        class="box">
                    <p>Confirm password :</p>
                    <input type="password" name="cpass" placeholder="Confirm your new password here" maxlength="20"
                        class="box">
                </div>
            </div>
            <p>Update picture :</p>
            <input type="file" name="image" accept="image/*" class="box">
            <input type="submit" name="submit" value="update now" class="btn">
        </form>

    </section>

    <!-- Section de mise à jour du profil se termine -->
