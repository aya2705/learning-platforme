<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
} else {
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="quick-select">

        <h1 class="heading">Announcements</h1>

        <div class="box-container">

            <?php
         // Sélection des annonces depuis la base de données où le statut est actif et les ordonner par date de création par ordre décroissant
         $select_announcements = $conn->prepare("SELECT * FROM `Announcements` WHERE status = ? ORDER BY created_at DESC");
         $select_announcements->execute(['active']);

         if ($select_announcements->rowCount() > 0) {
            // Parcourt chaque annonce récupérée de la base de données

            while ($fetch_announcement = $select_announcements->fetch(PDO::FETCH_ASSOC)) {
         ?>
            <div class="box">
                <h3 class="title"><?= $fetch_announcement['title']; ?></h3>
                <!-- Date et heure de création de l'annonce -->
                <small><?= $fetch_announcement['created_at']; ?></small>
                <p class="content"><?= $fetch_announcement['content']; ?> </p>
            </div>
            <?php
            }
         } else {
            echo '<p class="empty">No announcements available!</p>';
         }
         ?>

        </div>

    </section>
    <!-- Section for displaying announcements ends -->

    <script src="js/script.js"></script>

</body>

</html>