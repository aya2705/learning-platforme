<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}



$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$select_comments->execute([$user_id]);
$total_comments = $select_comments->rowCount();

$select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
$select_bookmark->execute([$user_id]);
$total_bookmarked = $select_bookmark->rowCount();

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
    <link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&family=Sedan+SC&display=swap" rel="stylesheet">
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
    <style>
    .description {
        background: radial-gradient(39.56% 48.29% at 20% 115.78%, #FF1675 0%, rgba(255, 22, 121, 0) 100%), radial-gradient(54.23% 74.52% at 69.72% -10.08%, #FF1675 0%, rgba(255, 22, 121, 0) 100%), radial-gradient(21.67% 31.7% at 39.72% 107.79%, rgba(74, 51, 209, 0.8) 0%, rgba(74, 51, 209, 0) 100%), radial-gradient(40.08% 51.33% at 85.83% 24.14%, rgba(74, 51, 209, 0.8) 0%, rgba(74, 51, 209, 0) 100%), #242145 !important;
        padding: 60px 20px;
        /* Adjusted padding: 40px top and bottom, 20px left and right */
        border-radius: 10px;
        border: 5px solid #39A1A7;
    }

    .description-container {
        font-family: Arial, sans-serif;
        color: #333;
        font-size: 16px;
        line-height: 1.6;
        display: flex;
        /* Center align content */
        align-items: flex-start;
        /* Align items to the start of the flex container */
    }

    .text-container {
        margin-top: 60px;
        flex: 1;
        /* Take remaining space */
    }

    .description-container h3 {
        font-weight: bold;
        color: #ff67a5;
        font-size: 16px;
        margin-bottom: 10px;
    }

    .description-container h1 {
        font-weight: bold;
        color: white;
        /* Blue color */
        margin-top: 0px;
        margin-bottom: 10px;
        /* Add some space below the heading */
        font-size: 32px;
        font-family: "Racing Sans One", sans-serif;
        text-align: justify;
    }

    .description-image {
        max-width: 50%;
        /* Adjust the width of the image as needed */
        margin-left: 20px;
        /* Add some space between the paragraph and the image */
    }

    .description-container p {
        font-style: italic;
        font-weight: bold;
        color: #39A1A7;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <?php include 'components/user_header.php'; ?>
    <?php if(empty($user_id)): ?>
    <section class="description">
        <div class="description-container">
            <div class="text-container">
                <h1>Welcome to EduHub, </h1>
                <h3>"Unlock your potential. Embrace the journey of learning."</h3>
                <p>At our learning platform, we believe in empowering individuals to thrive through knowledge. Our
                    comprehensive resources and interactive tools are designed to inspire curiosity, foster creativity,
                    and drive meaningful learning experiences. Whether you're a student, professional, or lifelong
                    learner, discover endless opportunities to expand your horizons and unlock your full potential with
                    us. </p>
                <a href="teacher/register.php" class="inline-btn">Get started as teacher</a>
                <a href="register.php" class="inline-btn">Get started as student</a>
            </div>
            <img src="uploaded_files/platform.png" class="description-image">
        </div>
    </section>
    <?php endif; ?>


    <!-- quick select section starts  -->

    <section class="quick-select">
        <div class="box-container">

            <?php
         if($user_id != ''){
      ?>
            <div class="box">
                <h3 class="title">Subscriptions</h3>
                <p>Saved playlist : <span><?= $total_bookmarked; ?></span></p>
                <a href="bookmark.php" class="inline-btn">View subscriptions</a>
            </div>
            <?php
         }else{ 
      ?>

            <?php  
      }
      ?>

            <div class="box">
                <h3 class="title">Modules</h3>
                <div class="flex">
                    <a><i class="fas fa-code"></i><span>Web development</span></a>
                    <a><i class="fa-solid fa-gears"></i><span>Digital Electronics</span></a>
                    <a><i class="fa-solid fa-c"></i><span>Advanced Programming</span></a>
                    <a><i class="fa-brands fa-contao"></i><span>Compilation & Language </span></a>
                    <a><i class="fas fa-cog"></i><span>Operating systems</span></a>
                    <a><i class="fa-solid fa-laptop-code"></i><span>Computer Architecture</span></a>

                </div>
            </div>

        </div>

    </section>

    <!-- quick select section ends -->

    <!-- courses section starts  -->

    <section class="courses">

        <h1 class="heading">Latest courses</h1>

        <div class="box-container">

            <?php
         $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE status = ? ORDER BY date DESC LIMIT 6");
         $select_courses->execute(['active']);
         if($select_courses->rowCount() > 0){
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id'];

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
                <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
                <h3 class="title"><?= $fetch_course['title']; ?></h3>
                <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">View playlist</a>
            </div>
            <?php
         }
      }else{
         echo '<p class="empty">No courses added yet!</p>';
      }
      ?>

        </div>

        <div class="more-btn">
            <a href="courses.php" class="inline-option-btn">View more</a>
        </div>

    </section>

    <!-- courses section ends -->














    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>