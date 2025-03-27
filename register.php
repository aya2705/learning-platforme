<?php
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

// Vérifier si le formulaire d'inscription est soumis
if(isset($_POST['submit'])){


   // Générer un identifiant unique
   $id = unique_id();

   // Récupérer et filtrer le nom de l'utilisateur
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   // Récupérer et filtrer l'e-mail de l'utilisateur
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   // Récupérer, hacher et filtrer le mot de passe de l'utilisateur
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   // Récupérer, hacher et filtrer la confirmation du mot de passe de l'utilisateur
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   // Récupérer le nom de l'image téléchargée et le filtrer
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);

   // Récupérer l'extension de l'image
   $ext = pathinfo($image, PATHINFO_EXTENSION);

   // Renommer le fichier téléchargé avec un identifiant unique et son extension
   $rename = unique_id().'.'.$ext;

   // Récupérer la taille de l'image téléchargée
   $image_size = $_FILES['image']['size'];

   // Récupérer le nom temporaire du fichier téléchargé
   $image_tmp_name = $_FILES['image']['tmp_name'];

   // Définir le chemin du dossier de destination pour l'image téléchargée
   $image_folder = 'uploaded_files/'.$rename;

   // Vérifier si l'e-mail est déjà pris
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);
   
   // Si l'e-mail est déjà pris, afficher un message
   if($select_user->rowCount() > 0){
      $message[] = 'email already taken!';
   }else{
      // Sinon, vérifier si les mots de passe correspondent
      if($pass != $cpass){
         $message[] = 'confirm passowrd not matched!';
      }else{
         // Si les mots de passe correspondent, insérer l'utilisateur dans la base de données
         $insert_user = $conn->prepare("INSERT INTO `users`(id, name, email, password, image) VALUES(?,?,?,?,?)");
         $insert_user->execute([$id, $name, $email, $cpass, $rename]);

         // Déplacer l'image téléchargée vers le dossier de destination
         move_uploaded_file($image_tmp_name, $image_folder);
         
         // Vérifier l'utilisateur nouvellement enregistré
         $verify_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? LIMIT 1");
         $verify_user->execute([$email, $pass]);
         $row = $verify_user->fetch(PDO::FETCH_ASSOC);
         
         // Si l'utilisateur est vérifié, définir un cookie d'identification et rediriger vers la page d'accueil
         if($verify_user->rowCount() > 0){
            setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');
            header('location:home.php');
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
    <title>Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <section class="form-container">

        <form class="register" action="" method="post" enctype="multipart/form-data">
            <h3>Create account</h3>
            <div class="flex">
                <div class="col">
                    <p>Your name <span>*</span></p>
                    <input type="text" name="name" placeholder="enter your name" maxlength="50" required class="box">
                    <p>Your email <span>*</span></p>
                    <input type="email" name="email" placeholder="enter your email" maxlength="50" required class="box">
                </div>
                <div class="col">
                    <p>Your password <span>*</span></p>
                    <input type="password" name="pass" placeholder="enter your password" maxlength="50" required
                        class="box">
                    <p>Confirm password <span>*</span></p>
                    <input type="password" name="cpass" placeholder="confirm your password" maxlength="50" required
                        class="box">
                </div>
            </div>
            <p>Select pic <span>*</span></p>
            <input type="file" name="image" accept="image/*" required class="box">
            <p class="link">Already have an account? <a href="login.php">Login now</a></p>
            <input type="submit" name="submit" value="register now" class="btn">
        </form>

    </section>













    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>