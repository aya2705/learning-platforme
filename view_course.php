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

// Vérifie si l'identifiant de cours est présent dans l'URL
if(isset($_GET['course_id'])){
    // Récupère l'identifiant du cours à partir de l'URL
    $course_id = $_GET['course_id'];

    // Sélectionne les détails du cours en fonction de son identifiant
    $select_course = $conn->prepare("SELECT * FROM `content` WHERE id = ?");
    $select_course->execute([$course_id]);
    // Récupère les détails du cours
    $fetch_course = $select_course->fetch(PDO::FETCH_ASSOC);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

    <style>
    /* Custom CSS for course details */
    .course-details {
        padding: 50px;
        background: radial-gradient(39.56% 48.29% at 20% 115.78%, #ffafbd 0%, rgba(255, 175, 189, 0) 100%),
            radial-gradient(54.23% 74.52% at 69.72% -10.08%, #ffafbd 0%, rgba(255, 175, 189, 0) 100%),
            radial-gradient(21.67% 31.7% at 39.72% 107.79%, rgba(0, 152, 122, 0.267) 0%, rgba(0, 152, 122, 0) 100%),
            radial-gradient(40.08% 51.33% at 85.83% 24.14%, rgba(0, 152, 122, 0.267) 0%, rgba(0, 152, 122, 0) 100%),
            #242145 !important;
        background: radial-gradient(39.56% 48.29% at 20% 115.78%, #ffafbd 0%, rgba(255, 175, 189, 0) 100%),
            radial-gradient(54.23% 74.52% at 69.72% -10.08%, #ffafbd 0%, rgba(255, 175, 189, 0) 100%),
            radial-gradient(21.67% 31.7% at 39.72% 107.79%, rgba(0, 152, 122, 0.267) 0%, rgba(0, 152, 122, 0) 100%),
            radial-gradient(40.08% 51.33% at 85.83% 24.14%, rgba(0, 152, 122, 0.267) 0%, rgba(0, 152, 122, 0) 100%),
            #242145 !important;
        /* Change background color to blue */
        color: white;
        /* Change text color to white */
        font-family: 'Poppins', sans-serif;
    }

    .heading {
        text-align: center;
        margin-bottom: 30px;
        font-size: 2.5rem;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .box-container {
        display: flex;
        justify-content: center;
        align-items: center;

    }

    .box {
        background: radial-gradient(39.56% 48.29% at 20% 115.78%, #ffafbd 0%, rgba(255, 175, 189, 0) 100%),
            radial-gradient(54.23% 74.52% at 69.72% -10.08%, #ffafbd 0%, rgba(255, 175, 189, 0) 100%),
            radial-gradient(21.67% 31.7% at 39.72% 107.79%, rgba(0, 152, 122, 0.267) 0%, rgba(0, 152, 122, 0) 100%),
            radial-gradient(40.08% 51.33% at 85.83% 24.14%, rgba(0, 152, 122, 0.267) 0%, rgba(0, 152, 122, 0) 100%),
            #242145 !important;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        width: 100%;
    }

    .tutor {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .tutor img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-right: 20px;
    }

    .tutor h3 {
        font-size: 1.9rem;
        margin-bottom: 5px;


    }

    .tutor span {
        font-size: 1rem;
    }

    .thumb {
        width: 100%;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .description {
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 20px;
        text-align: justify;
    }

    .prerequisites {
        margin-bottom: 20px;
    }

    .prerequisites h3 {
        font-size: 1.3rem;
        margin-bottom: 10px;
        color: red;
    }

    .prerequisites p {
        font-size: 1.1rem;
        line-height: 1.6;
        text-align: justify;
    }

    .pdf-container {
        border-top: 1px solid #ccc;
        padding-top: 20px;
    }

    .pdf-title {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: blue;
    }

    iframe {
        width: 100%;
        height: 600px;
        border: none;
    }
    </style>

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <!-- course details section starts  -->

    <section class="course-details">

        <h1 class="heading"><?= $fetch_course['title']; ?></h1>

        <div class="box-container">
            <div class="box">
                <div class="tutor">
                    <?php
                        $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
                        $select_tutor->execute([$fetch_course['tutor_id']]);
                        $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
                    <div>
                        <h3><?= $fetch_tutor['name']; ?></h3>
                    </div>
                </div>
                <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
                <div class="prerequisites">
                    <h3>Description :</h3>
                    <p class="description"><?= $fetch_course['description']; ?></p>
                </div>

                <?php if (!empty($fetch_course['prerequisites'])) : ?>
                <div class="prerequisites">
                    <h3>Prerequisites :</h3>
                    <p><?= $fetch_course['prerequisites']; ?></p>
                </div>
                <?php endif; ?>
                <div class="pdf-container">
                    <h3 class="pdf-title">Course PDF :</h3>
                    <iframe src="uploaded_files/<?= $fetch_course['video']; ?>" frameborder="0"></iframe>
                </div>
                <h2>Note: If you want to leave a comment about this lesson, go to the Lessons button and you will find a
                    box in which you can write your comment</h2>
            </div>
        </div>

    </section>

    <!-- course details section ends -->

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>