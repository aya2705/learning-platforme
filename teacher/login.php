<?php

// Inclusion du fichier de connexion à la base de données
include '../components/connect.php';

// Vérification si le formulaire a été soumis
if(isset($_POST['submit'])){

   // Récupération et filtration de l'email
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   // Récupération et hashage du mot de passe
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   // Préparation et exécution de la requête pour sélectionner le tuteur
   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email = ? AND password = ? LIMIT 1");
   $select_tutor->execute([$email, $pass]);
   $row = $select_tutor->fetch(PDO::FETCH_ASSOC);
   
   // Vérification si un tuteur a été trouvé
   if($select_tutor->rowCount() > 0){
     // Création d'un cookie pour le tuteur et redirection vers le tableau de bord
     setcookie('tutor_id', $row['id'], time() + 60*60*24*30, '/');
     header('location:dashboard.php');
   }else{
      // Ajout d'un message d'erreur si l'email ou le mot de passe est incorrect
      $message[] = 'incorrect email or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Lien CDN pour les icônes Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Lien vers le fichier CSS personnalisé -->
    <link rel="stylesheet" href="../css/teacher_style.css">

</head>

<body style="padding-left: 0;">

    <?php
    // Affichage des messages s'il y en a
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

    <!-- Début de la section de connexion -->

    <section class="form-container">

        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Hi, welcome back!</h3>
            <p>Your email <span>*</span></p>
            <input type="email" name="email" placeholder="enter your email" maxlength="50" required class="box">
            <p>Your password <span>*</span></p>
            <input type="password" name="pass" placeholder="enter your password" maxlength="50" required class="box">
            <p class="link">Don't have an account? <a href="register.php">Register</a></p>
            <input type="submit" name="submit" value="login now" class="btn">
        </form>

    </section>

    <!-- Fin de la section de connexion -->

</body>

</html>
