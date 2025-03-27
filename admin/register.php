<?php

include '../components/connect.php';

if(isset($_POST['submit'])){  // Vérifie si le formulaire a été soumis

   $id = unique_id(); // Génère un identifiant unique pour le nouvel utilisateur
   $name = $_POST['name'];  // Récupère le nom de l'utilisateur depuis le formulaire
   $name = filter_var($name, FILTER_SANITIZE_STRING);// Nettoie le nom pour éliminer les balises HTML et PHP
   $email = $_POST['email']; // Récupère l'email depuis le formulaire
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   $pass = sha1($_POST['pass']);  // Hasher le mot de passe en utilisant SHA1
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);// Nettoie le mot de passe chiffré
   $cpass = sha1($_POST['cpass']);// Hasher le mot de passe en utilisant SHA1
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name']; // Récupère le nom du fichier image depuis le formulaire
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION); // Extrait l'extension du fichier image
   $rename = unique_id().'.'.$ext; // Renomme le fichier image avec un ID unique et conserve l'extension originale
   $image_size = $_FILES['image']['size']; // Récupère la taille temporaire du fichier image
   $image_tmp_name = $_FILES['image']['tmp_name']; // Récupère le nom temporaire du fichier image
   $image_folder = '../uploaded_files/'.$rename;  // Définit le chemin du dossier où l'image sera sauvegardée

   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE email = ?"); // Prépare une requête SQL pour rechercher un administrateur avec le même email
   $select_admin->execute([$email]); // Exécute la requête avec l'email fourni
   
   if($select_admin->rowCount() > 0){
      $message[] = 'email already taken!'; // Si un administrateur avec le même email existe déjà, ajoute un message d'erreur
   }else{
      if($pass != $cpass){
         $message[] = 'confirm passowrd not matched!'; // Vérifie si les mots de passe correspondent, sinon ajoute un message d'erreur
      }else{
         // Prépare une requête SQL pour insérer un nouvel administrateur dans la base de données
         $insert_admin = $conn->prepare("INSERT INTO `admin`(id, name, email, password, image) VALUES(?,?,?,?,?)");
         // Exécute la requête avec les valeurs fournies
         $insert_admin->execute([$id, $name, $email, $cpass, $rename]);
          // Déplace l'image téléchargée dans le dossier spécifié
         move_uploaded_file($image_tmp_name, $image_folder);
          // Ajoute un message de succès indiquant que le nouvel administrateur a été enregistré
         $message[] = 'new admin registered! please login now';
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
   

   <!-- Importer des fonts de l'internet -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Importer le fichier css -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body style="padding-left: 0;">

<?php
 // gérer et afficher des messages dynamiques à l'utilisateur,
if(isset($message)){
 if(is_array($message)){

   foreach($message as $message){
      echo '
      <div class="message form">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
 }
}
?>

<section class="form-container">

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>register new</h3>
      <div class="flex">
         <div class="col">
            <p>Your name <span>*</span></p>
            <input type="text" name="name" placeholder="enter your name" maxlength="50" required class="box">
            <p>Your email <span>*</span></p>
            <input type="email" name="email" placeholder="enter your email" maxlength="20" required class="box">
         </div>
         <div class="col">
            <p>Your password <span>*</span></p>
            <input type="password" name="pass" placeholder="enter your password" maxlength="20" required class="box">
            <p>Confirm password <span>*</span></p>
            <input type="password" name="cpass" placeholder="confirm your password" maxlength="20" required class="box">
            <p>Select pic <span>*</span></p>
            <input type="file" name="image" accept="image/*" required class="box">
         </div>
      </div>
      <p class="link">Already have an account? <a href="login.php">Login now</a></p>
      <input type="submit" name="submit" value="register now" class="btn">
   </form>

</section>

<script>
//Récupération de l'état du mode sombre 
let darkMode = localStorage.getItem('dark-mode');
// Sélection du corps du document
let body = document.body;
// Fonction pour activer le mode sombre 
const enabelDarkMode = () =>{
   body.classList.add('dark');
   localStorage.setItem('dark-mode', 'enabled');
}
// Fonction pour désactiver le mode sombre
const disableDarkMode = () =>{
   body.classList.remove('dark');
   localStorage.setItem('dark-mode', 'disabled');
}
// Logique conditionnelle pour définir le mode sombre
if(darkMode === 'enabled'){
   enabelDarkMode();
}else{
   disableDarkMode();
}

</script>
   
</body>
</html>