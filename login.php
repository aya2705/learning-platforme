<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_POST['submit'])){

    // Récupérer et filtrer l'email et le mot de passe soumis par le formulaire
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
 
    // Sélectionner l'utilisateur correspondant à l'email et au mot de passe fournis
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? LIMIT 1");
    $select_user->execute([$email, $pass]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);
    
    // Vérifier si l'utilisateur existe dans la base de données
    if($select_user->rowCount() > 0){
      // Créer un cookie contenant l'ID de l'utilisateur pour une durée d'un mois
      setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');
      // Rediriger l'utilisateur vers la page d'accueil
      header('location:home.php');
    }else{
       // Afficher un message d'erreur si l'email ou le mot de passe est incorrect
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

        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Welcome back!</h3>
            <p>Your email <span>*</span></p>
            <input type="email" name="email" placeholder="enter your email" maxlength="50" required class="box">
            <p>Your password <span>*</span></p>
            <input type="password" name="pass" placeholder="enter your password" maxlength="50" required class="box">
            <p class="link">Don't have an account? <a href="register.php">Register now</a></p>
            <input type="submit" name="submit" value="Login now" class="btn">
        </form>

    </section>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>