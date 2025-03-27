<?php

include '../components/connect.php';

if(isset($_POST['submit'])){ // Vérifier si le formulaire a été soumis

   $email = $_POST['email'];  // Récupération de  l'email depuis le formulaire
   $email = filter_var($email, FILTER_SANITIZE_STRING); // Nettoyer la variable email pour éviter les injections SQL
   $pass = sha1($_POST['pass']);  // Hacher le mot de passe à l'aide de SHA1
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    // Préparer la requête SQL pour sélectionner l'administrateur
   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE email = ? AND password = ? LIMIT 1");
   $select_admin->execute([$email, $pass]); // Exécuter la requête avec l'email et le mot de passe
   $row = $select_admin->fetch(PDO::FETCH_ASSOC); //recuperation des resultats
   
    // Si un administrateur est trouvé
   if($select_admin->rowCount() > 0){// Créer un cookie pour l'ID de l'administrateur
      setcookie('tutor_id', $row['id'], time() + 60*60*24*30, '/');
      header('location:dashboard.php');
   }else{
      $message[] = 'incorrect email or password!';// Stocker un message d'erreur si l'email ou le mot de passe est incorrect
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
   

   <!-- Importer des fonts de l'internet -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Importer le fichier css -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body style="padding-left: 0;">

<?php
if(isset($message)){   // Vérifier si la variable $message est définie
 if(is_array($message)){  // Vérifier si la variable $message est un tableau

   foreach($message as $message){  // Parcourir chaque message dans le tableau $message
      echo '
      <div class="message form"> // Créer un div pour afficher le message
         <span>'.$message.'</span> //afficher le message
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
 }
}
?>


<!-- Section pour contenir le formulaire avec une classe pour le style -->
<section class="form-container">
   <!-- Formulaire de connexion avec méthode POST et encodage pour les données multipart/form-data -->
   <form action="" method="post" enctype="multipart/form-data" class="login">
      <h3>Welcome back Admin!</h3>
      <p>your email <span>*</span></p>
      <input type="email" name="email" placeholder="enter your email" maxlength="40" required class="box">
      <p>your password <span>*</span></p>
      <input type="password" name="pass" placeholder="enter your password" maxlength="40" required class="box">
      
      <input type="submit" name="submit" value="login now" class="btn">
   </form>

</section>




   
</body>
</html>