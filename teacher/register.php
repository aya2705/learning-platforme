<?php

// Inclusion du fichier de connexion à la base de données
include '../components/connect.php';

// Vérification si le formulaire a été soumis
if(isset($_POST['submit'])){

   // Génération d'un identifiant unique pour le tuteur
   $id = unique_id();

   // Récupération et filtrage du nom
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   // Récupération et filtrage de l'email
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   // Hashage et filtrage du mot de passe
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   // Hashage et filtrage de la confirmation du mot de passe
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   // Gestion de l'image de profil
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/'.$rename;

   // Vérification si l'email existe déjà dans la base de données
   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email = ?");
   $select_tutor->execute([$email]);
   
   if($select_tutor->rowCount() > 0){
      // Message si l'email est déjà pris
      $message[] = 'Email already taken!';
   }else{
      // Vérification si les mots de passe correspondent
      if($pass != $cpass){
         $message[] = 'Confirm password not matched!';
      }else{
         // Insertion du nouveau tuteur dans la base de données
         $insert_tutor = $conn->prepare("INSERT INTO `tutors`(id, name, email, password, image) VALUES(?,?,?,?,?)");
         $insert_tutor->execute([$id, $name, $email, $cpass, $rename]);

         // Déplacement de l'image téléchargée vers le dossier spécifié
         move_uploaded_file($image_tmp_name, $image_folder);
         $message[] = 'New tutor registered! please login now';
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
    <title>register</title>

    <!-- Lien CDN pour les icônes Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Lien vers le fichier CSS personnalisé -->
    <link rel="stylesheet" href="../css/teacher_style.css">

</head>

<body style="padding-left: 0;">

    <?php
    // Affichage des messages d'erreur ou de succès
    if(isset($message)){
       foreach($message as $message){
          echo '
          <div class="message form">
             <span>'.$message.'</span>
             <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
          </div>
          ';
       }
    }
    ?>

    <!-- La section d'inscription commence -->
    <section class="form-container">

        <form class="register" action="" method="post" enctype="multipart/form-data">
            <h3>register</h3>
            <div class="flex">
                <div class="col">
                    <p>Your name <span>*</span></p>
                    <input type="text" name="name" placeholder="enter your name here" maxlength="50" required
                        class="box">
                    <p>Your email <span>*</span></p>
                    <input type="email" name="email" placeholder="enter your email here" maxlength="20" required
                        class="box">
                </div>
                <div class="col">
                    <p>Your password <span>*</span></p>
                    <input type="password" name="pass" placeholder="enter your password here" maxlength="20" required
                        class="box">
                    <p>Confirm your password <span>*</span></p>
                    <input type="password" name="cpass" placeholder="confirm your password here" maxlength="20" required
                        class="box">
                    <p>Select pic <span>*</span></p>
                    <input type="file" name="image" accept="image/*" required class="box">
                </div>
            </div>
            <p class="link">Do you have an account? <a href="login.php">Login now</a></p>
            <input type="submit" name="submit" value="register now" class="btn">
        </form>

    </section>

    <!-- La section d'inscription se termine -->

    <script>
    // Gestion du mode sombre
    let darkMode = localStorage.getItem('dark-mode');
    let body = document.body;

    const enabelDarkMode = () => {
        body.classList.add('dark');
        localStorage.setItem('dark-mode', 'enabled');
    }

    const disableDarkMode = () => {
        body.classList.remove('dark');
        localStorage.setItem('dark-mode', 'disabled');
    }

    // Vérification et application du mode sombre en fonction des préférences de l'utilisateur
    if (darkMode === 'enabled') {
        enabelDarkMode();
    } else {
        disableDarkMode();
    }
    </script>

</body>

</html>
