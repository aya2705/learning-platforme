<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

if($tutor_id != ''){ 
    // Check if the content ID is valid
    $select_content = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ? LIMIT 1");
    $select_content->execute([$tutor_id]);
    // Check if any content was fetched
    if ($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)) {
        // Define $content_id after fetching content
        $content_id = $fetch_content['id']; // Assuming 'id' is the column name for content ID

        if(isset($_POST['add_comment']) && isset($_POST['comment_box'])){
            $id = unique_id();
            $comment_box = $_POST['comment_box'];
            $comment_box = filter_var($comment_box, FILTER_SANITIZE_STRING);
            $content_id = $_POST['content_id'];
            $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

            if($select_content->rowCount() > 0){
                // Retrieve the tutor ID associated with the content
                $tutor_id = $fetch_content['tutor_id'];
                
                // Determine the parent ID for the new comment
                $parent_id = null; // Initialize parent ID as null by default
                $select_last_comment = $conn->prepare("SELECT id FROM `comments` WHERE content_id = ? ORDER BY date DESC LIMIT 1");
                $select_last_comment->execute([$fetch_content['id']]);
                $fetch_last_comment = $select_last_comment->fetch(PDO::FETCH_ASSOC);
                if($select_last_comment->rowCount() > 0){
                    $parent_id = $fetch_last_comment['id']; // Set parent ID to the last comment's ID
                }
                
                // Insert the new comment
                $insert_comment = $conn->prepare("INSERT INTO `comments`(id, content_id, user_id, tutor_id, comment, parent_id) VALUES(?,?,?,?,?,?)");
                $insert_comment->execute([$id, $content_id, null, $tutor_id, $comment_box, $parent_id]);
                $message[] = 'New comment added!';
                
                // Redirect back to the same page to prevent form resubmission on page refresh
                header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
                exit();
            }else{
                $message[] = 'Invalid content ID!';
            }
        }else{
            $message[] = 'Please login first!';
        }
    } 
}

if(isset($_POST['delete_comment'])){
   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   if($verify_comment->rowCount() > 0){
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      $message[] = 'Comment deleted successfully!';
   }else{
      $message[] = 'Comment already deleted!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>

<body>

    <?php include '../components/teacher_header.php'; ?>

    <section class="comments">

        <h1 class="heading">User Comments</h1>

        <div class="show-comments">
            <?php
            if (isset($content_id)) {
                $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE content_id=? and parent_id is null");
                $select_comments->execute([$content_id]);
                while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){
                    $select_replies = $conn->prepare("SELECT * FROM `comments` WHERE parent_id=?");
                    $select_replies->execute([$fetch_comment['id']]);
                    $fetch_replies = $select_replies->fetchAll(PDO::FETCH_ASSOC);

                    $select_student = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                    $select_student->execute([$fetch_comment['user_id']]);
                    $fetch_student = $select_student->fetch(PDO::FETCH_ASSOC);
            ?>
                    <div class="box" style="<?php if($fetch_comment['tutor_id'] == $tutor_id){echo 'order:-1;';} ?>">
                        <div class="content"><span><?= $fetch_comment['date']; ?></span>
                            <p>- <?= $fetch_content['title']; ?> - </p><a
                                href="view_content.php?get_id=<?= $fetch_content['id']; ?>">View content</a>
                        </div>
                        <p class="text"><?= $fetch_comment['comment']; ?></p>

                        <?php foreach ($fetch_replies as $reply) : ?>
                            <?php if ($reply['parent_id'] == $fetch_comment['id']) : ?>
                                <div class="reply">
                                    <span><?= $reply['date']; ?></span>
                                    <p class="text"><?= $reply['comment']; ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <form action="" method="post" class="add-comment">
                            <input type="hidden" name="content_id" value="<?= $fetch_content['id']; ?>">
                            <textarea name="comment_box" required placeholder="Write your comment..." maxlength="1000" cols="30" rows="10"></textarea>
                            <input type="submit" value="Add Comment" name="add_comment" class="inline-btn">
                        </form>
                    </div>
            <?php 
                }
            }
           ?>
        </div>

    </section>

    <script src="../js/teacher_script.js"></script>

</body>

</html>
