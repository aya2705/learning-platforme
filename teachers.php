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
    <title>teachers</title>
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

    <?php include 'components/user_header.php'; ?>

    <!-- teachers section starts  -->

    <section class="teachers">

        <h1 class="heading">Teachers</h1>

        <form action="search_tutor.php" method="post" class="search-tutor">
            <input type="text" name="search_tutor" maxlength="100" placeholder="search tutor..." required>
            <button type="submit" name="search_tutor_btn" class="fas fa-search"></button>
        </form>

        <div class="box-container">



            <?php
         // Sélectionne tous les tuteurs de la base de données
         $select_tutors = $conn->prepare("SELECT * FROM `tutors`");
         $select_tutors->execute();
         // Vérifie s'il y a des tuteurs dans la base de données
         if($select_tutors->rowCount() > 0){
            // Parcourt chaque tuteur trouvé
            while($fetch_tutor = $select_tutors->fetch(PDO::FETCH_ASSOC)){

               $tutor_id = $fetch_tutor['id'];

               // Compte le nombre de listes de lecture associées à ce tuteur
               $count_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
               $count_playlists->execute([$tutor_id]);
               $total_playlists = $count_playlists->rowCount();

               // Compte le nombre de contenus associés à ce tuteur
               $count_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
               $count_contents->execute([$tutor_id]);
               $total_contents = $count_contents->rowCount();

               // Compte le nombre de commentaires associés à ce tuteur
               $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
               $count_comments->execute([$tutor_id]);
               $total_comments = $count_comments->rowCount();
      ?>

            <div class="box">
                <div class="tutor">
                    <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
                    <div>
                        <h3><?= $fetch_tutor['name']; ?></h3>
                    </div>
                </div>
                <p>playlists : <span><?= $total_playlists; ?></span></p>
                <p>total videos : <span><?= $total_contents ?></span></p>
                <p>total comments : <span><?= $total_comments ?></span></p>
                <form action="tutor_profile.php" method="post">
                    <input type="hidden" name="tutor_email" value="<?= $fetch_tutor['email']; ?>">
                    <input type="submit" value="view profile" name="tutor_fetch" class="inline-btn">
                </form>
            </div>
            <?php
            }
         }else{
            echo '<p class="empty">no tutors found!</p>';
         }
      ?>

        </div>

    </section>

    <!-- teachers section ends -->































    <script src="js/script.js"></script>

</body>

</html>