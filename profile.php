<?php

// Inclure le fichier de connexion à la base de données
include 'components/connect.php';

// Vérifier si l'identifiant de l'utilisateur est défini dans les cookies
if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];
}else{
    $user_id = '';
    // Rediriger vers la page de connexion si l'identifiant de l'utilisateur n'est pas défini
    header('location:login.php');
}

// Sélectionner les commentaires de l'utilisateur
$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$select_comments->execute([$user_id]);
$total_comments = $select_comments->rowCount(); // Nombre total de commentaires de l'utilisateur

// Sélectionner les playlists bookmarkées par l'utilisateur
$select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
$select_bookmark->execute([$user_id]);
$total_bookmarked = $select_bookmark->rowCount(); // Nombre total de playlists bookmarkées par l'utilisateur

$message = ''; // Initialiser le message

// Traiter la soumission du formulaire pour supprimer le compte utilisateur
if(isset($_POST['delete_account'])) {
    // Insérer la demande de suppression dans la table deletion_requests
    $insert_request = $conn->prepare("INSERT INTO deletion_requests (user_id) VALUES (?)");
    $insert_request->execute([$user_id]);
    
    // Afficher un message de confirmation
    $message = "Your deletion request has been sent to the administrator for review";
}




// Vérifier si la demande de suppression a été acceptée
$select_request_status = $conn->prepare("SELECT admin_id FROM deletion_requests WHERE user_id = ?");
$select_request_status->execute([$user_id]);
$request_status = $select_request_status->fetchColumn();

if($request_status) {
// Si la demande est acceptée, rediriger l'utilisateur vers la page register.php
header('location: register.php');
exit(); // Terminer le script pour éviter toute exécution supplémentaire
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="profile">

        <h1 class="heading">Profile Details</h1>

        <div class="details">

            <div class="user">

                <div class="user">
                    <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
                    <h3><?= $fetch_profile['name']; ?></h3>
                    <p>Student</p>
                    <a href="update.php" class="inline-btn">Update Profile</a>
                    <form action="profile.php" method="post">
                        <button type="submit" name="delete_account" class="account-btn">Delete My Account</button>
                    </form>
                    <?php if(!empty($message)): ?>
                    <p><?php echo $message; ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="box-container">
                <div class="box">
                    <div class="flex">
                        <i class="fas fa-bookmark"></i>
                        <div>
                            <h3><?= $total_bookmarked; ?></h3>
                            <span>Saved Playlists</span>
                        </div>
                    </div>
                    <a href="#" class="inline-btn">View Playlists</a>
                </div>

                <div class="box">
                    <div class="flex">

                        <i class="fas fa-comment"></i>
                        <div>
                            <h3><?= $total_comments; ?></h3>
                            <span>Video Comments</span>
                        </div>
                    </div>
                    <a href="#" class="inline-btn">View Comments</a>
                </div>

            </div>

        </div>

    </section>


    <script src="js/script.js"></script>

</body>

</html>