<?php

include 'components/connect.php';

// Vérifie si un identifiant d'utilisateur est présent dans les cookies
if(isset($_COOKIE['user_id'])){
   // Récupère l'identifiant d'utilisateur à partir des cookies
   $user_id = $_COOKIE['user_id'];
}else{
   // Initialise l'identifiant d'utilisateur à une chaîne vide si aucun n'est trouvé dans les cookies
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

    <section class="teachers">

        <h1 class="heading">Teachers</h1>

        <form action="" method="post" class="search-tutor">
            <input type="text" name="search_tutor" maxlength="100" placeholder="Search tutor..." required>
            <button type="submit" name="search_tutor_btn" class="fas fa-search"></button>
        </form>

        <div class="box-container">

            <?php
         if(isset($_POST['search_tutor']) or isset($_POST['search_tutor_btn'])){
            $search_tutor = $_POST['search_tutor'];
            $select_tutors = $conn->prepare("SELECT * FROM `tutors` WHERE name LIKE '%{$search_tutor}%'");
            $select_tutors->execute();
            if($select_tutors->rowCount() > 0){
               while($fetch_tutor = $select_tutors->fetch(PDO::FETCH_ASSOC)){

                  $tutor_id = $fetch_tutor['id'];

                  $count_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
                  $count_playlists->execute([$tutor_id]);
                  $total_playlists = $count_playlists->rowCount();

                  $count_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
                  $count_contents->execute([$tutor_id]);
                  $total_contents = $count_contents->rowCount();

                  $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
                  $count_comments->execute([$tutor_id]);
                  $total_comments = $count_comments->rowCount();
      ?>
            <div class="box">
                <div class="tutor">
                    <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
                    <div>
                        <h3><?= $fetch_tutor['name']; ?></h3>
                        <span>Teacher</span>
                    </div>
                </div>
                <p>Playlists : <span><?= $total_playlists; ?></span></p>
                <p>Total courses : <span><?= $total_contents ?></span></p>
                <p>Total comments : <span><?= $total_comments ?></span></p>
                <form action="tutor_profile.php" method="post">
                    <input type="hidden" name="tutor_email" value="<?= $fetch_tutor['email']; ?>">
                    <input type="submit" value="View profile" name="tutor_fetch" class="inline-btn">
                </form>
            </div>
            <?php
               }
            }else{
               echo '<p class="empty">No results found!</p>';
            }
         }else{
            echo '<p class="empty">Please search something!</p>';
         }
      ?>

        </div>

    </section>

    <!-- teachers section ends -->











    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>