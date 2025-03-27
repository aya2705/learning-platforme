<?php

// Inclure le fichier de connexion à la base de données
include 'components/connect.php';

// Vérifier si le cookie 'user_id' est défini
if(isset($_COOKIE['user_id'])){
   // Si oui, récupérer la valeur du cookie 'user_id'
   $user_id = $_COOKIE['user_id'];
}else{
   // Sinon, définir $user_id comme une chaîne vide
   $user_id = '';
   // Rediriger vers la page d'accueil
   header('location:home.php');
}

// Vérifier si le formulaire 'delete_comment' est soumis
if(isset($_POST['delete_comment'])){

   // Récupérer l'ID du commentaire à supprimer depuis le formulaire
   $delete_id = $_POST['comment_id'];
   // Filtrer et nettoyer l'ID du commentaire
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Vérifier si le commentaire existe dans la base de données
   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   // Si le commentaire existe
   if($verify_comment->rowCount() > 0){
      // Supprimer le commentaire de la base de données
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      // Ajouter un message indiquant que le commentaire a été supprimé avec succès
      $message[] = 'comment deleted successfully!';
   }else{
      // Ajouter un message indiquant que le commentaire a déjà été supprimé
      $message[] = 'comment already deleted!';
   }

}

?>

if(isset($_POST['update_now'])){

$update_id = $_POST['update_id'];
$update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
$update_box = $_POST['update_box'];
$update_box = filter_var($update_box, FILTER_SANITIZE_STRING);

$verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? AND comment = ? ORDER BY date DESC");
$verify_comment->execute([$update_id, $update_box]);

if($verify_comment->rowCount() > 0){
$message[] = 'comment already added!';
}else{
$update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
$update_comment->execute([$update_box, $update_id]);
$message[] = 'comment edited successfully!';
}

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>user comments</title>
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


    <section class="comments">

        <h1 class="heading">your comments</h1>


        <div class="show-comments">
            <?php
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
         $select_comments->execute([$user_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){
               $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ?");
               $select_content->execute([$fetch_comment['content_id']]);
               $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);
      ?>
            <div class="box" style="<?php if($fetch_comment['user_id'] == $user_id){echo 'order:-1;';} ?>">
                <div class="content"><span><?= $fetch_comment['date']; ?></span>
                    <p> - <?= $fetch_content['title']; ?> - </p><a
                        href="watch_video.php?get_id=<?= $fetch_content['id']; ?>">view content</a>
                </div>
                <p class="text"><?= $fetch_comment['comment']; ?></p>
                <?php
            if($fetch_comment['user_id'] == $user_id){ 
         ?>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
           
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('delete this comment?');">delete comment</button>
         </form>
         <?php
         }
         ?>
            </div>
            <?php
       }
      }else{
         echo '<p class="empty">no comments added yet!</p>';
      }
      ?>
        </div>

    </section>












    <script src="js/script.js"></script>

</body>

</html>