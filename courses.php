<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>courses</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <!-- courses section starts  -->

    <section class="courses">

            <h1 class="heading">All courses</h1>

            <div class="box-container">

                <?php
         // Sélectionner tous les cours actifs par ordre décroissant de date
         $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE status = ? ORDER BY date DESC");
         $select_courses->execute(['active']);
         // Vérifier s'il y a des cours disponibles
         if($select_courses->rowCount() > 0){
            // Parcourir chaque cours
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id'];

               // Sélectionner les informations du tuteur associé à ce cours
               $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
               $select_tutor->execute([$fetch_course['tutor_id']]);
               $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
                <div class="box">
                    <div class="tutor">
                        <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
                        <div>
                            <h3><?= $fetch_tutor['name']; ?></h3>
                            <span><?= $fetch_course['date']; ?></span>
                        </div>
                    </div>
                    <!-- Afficher l'image miniature du cours -->
                    <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
                    <!-- Afficher le titre du cours -->
                    <h3 class="title"><?= $fetch_course['title']; ?></h3>
                    <!-- Lien pour afficher la playlist complète -->
                    <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">view playlist</a>
                </div>
                <?php
         }
      }else{
         // Afficher un message si aucun cours n'est ajouté
         echo '<p class="empty">no courses added yet!</p>';
      }
      ?>

            </div>


    </section>




    <script src="js/script.js"></script>

</body>

</html>