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


// Sélectionne tout le contenu associé au tuteur spécifié
$select_teacher_content = $conn->prepare("SELECT * FROM content WHERE tutor_id = ?");
// Exécute la requête préparée en remplaçant le marqueur de paramètre par la valeur de $tutor_id
$select_teacher_content->execute([$tutor_id]);
// Récupère tous les résultats de la requête sous forme de tableau associatif
$teacher_content = $select_teacher_content->fetchAll();

// Sélectionne tous les utilisateurs de la base de données
$select_users = $conn->query("SELECT * FROM users");
$users = $select_users->fetchAll();

// Sélectionne tous les tuteurs de la base de données
$select_tutors = $conn->query("SELECT * FROM tutors");
$tutors = $select_tutors->fetchAll();


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- Importer des fonts de l'internet -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Importer le fichier .css -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="dashboard">
<!-- Creer un tableau pour l'admin peut voir les enseignants connectes-->
   <h1 class="heading">Dashboard</h1>
    <h2 class="heading" >Teachers</h2>
         <table class="content-table">
            <thead>
               <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Password</th>
                  
               </tr>
            </thead>
            <tbody>
               <?php foreach ($tutors as $tutor) { ?>
                  <!-- Boucler sur chaque enseignant pour qu'il doit etre affiche à l'admin-->
                  <tr>
                     <td><?php echo $tutor['id']; ?></td>
                     <td><?php echo $tutor['name']; ?></td>
                     <td><?php echo $tutor['email']; ?></td>
                     <td><?php echo $tutor['password']; ?></td>
                    
                  </tr>
               <?php } ?>
            </tbody>
         </table>
      
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>

   <h2 class="heading" >Students</h2>
   <!-- Meme chose pour les etudiants,on doit juste creer un tableau et afficher les etudiants pour l'admin-->
   <table class="content-table">
            <thead>
               <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Password</th>
                 
               </tr>
            </thead>
            <tbody>
               <?php foreach ($users as $user) { ?>
                  <tr>
                     <td><?php echo $user['id']; ?></td>
                     <td><?php echo $user['name']; ?></td>
                     <td><?php echo $user['email']; ?></td>
                     <td><?php echo $user['password']; ?></td>
                   
                  </tr>
               <?php } ?>
            </tbody>
         </table>
     
</section>
<!-- importer le script js -->
<script src="../js/admin_script.js"></script>

</body>
</html>
