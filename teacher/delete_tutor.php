<?php
// Include the database connection file
include 'components/connect.php';

// Check if the tutor_id cookie is set, if not redirect to login page
if(isset($_COOKIE['tutor_id'])){
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location:login.php');
    exit(); // Ensure the script stops after redirection
}

// Select the tutor's name based on their ID
$select_tutor = $conn->prepare("SELECT name FROM tutors WHERE id = ?");
$select_tutor->execute([$tutor_id]);
$tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
$tutor_name = $tutor['name'];

$message = '';

// Check if the tutor deletion request has been submitted
if(isset($_POST['delete_teacher'])) {
    // Insert the deletion request into the 'deletion_tutors' table
    $insert_request = $conn->prepare("INSERT INTO deletion_tutors (tutor_id, tutor_name) VALUES (?, ?)");
    $insert_request->execute([$tutor_id, $tutor_name]);

    // Check if the request has been accepted or rejected by the admin
    $select_request_status = $conn->prepare("SELECT admin_id FROM deletion_tutors WHERE tutor_id = ?");
    $select_request_status->execute([$tutor_id]);
    $request_status = $select_request_status->fetchColumn();
    
    if($request_status) {
        // Delete associated contents
        $delete_contents = $conn->prepare("DELETE FROM content WHERE tutor_id = ?");
        $delete_contents->execute([$tutor_id]);

        // Delete associated playlists
        $delete_playlists = $conn->prepare("DELETE FROM playlist WHERE tutor_id = ?");
        $delete_playlists->execute([$tutor_id]);

        // Delete the tutor from the 'tutors' table
        $delete_tutor = $conn->prepare("DELETE FROM tutors WHERE id = ?");
        $delete_tutor->execute([$tutor_id]);

        // Delete the request from the 'deletion_tutors' table after deleting the tutor
        $delete_request = $conn->prepare("DELETE FROM deletion_tutors WHERE tutor_id = ?");
        $delete_request->execute([$tutor_id]);
        
        // Redirect the user to the home page
        header('location: home.php');
        exit(); // End the script to prevent further execution
    } else {
        // If the request is still pending or has been rejected, check if it was rejected by the admin
        $select_request_status = $conn->prepare("SELECT admin_id FROM deletion_tutors WHERE tutor_id = ?");
        $select_request_status->execute([$tutor_id]);
        $request_status = $select_request_status->fetchColumn();

        if(!$request_status) {
            // If the request was rejected, the confirmation message should disappear
            $message = '';
        } else {
            // If the request is still pending, display the message
            $message = "Your deletion request has been sent to the administrator for review";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Link to Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <!-- Link to custom CSS file -->
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>

<?php include '../components/teacher_header.php'; ?>

<section class="tutor-profile" style="min-height: calc(100vh - 19rem);">

    <h1 class="heading">Profile Details</h1>

    <div class="details">
        <div class="tutor">
            <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
            <h3><?= $fetch_profile['name']; ?></h3>
            <span>Teacher</span>
            <a href="update.php" class="inline-btn">Update Profile</a>
            <form action="profile.php" method="post">
                <button type="submit" name="delete_teacher" class="teacher-btn">Delete My Account</button>
            </form>
            <?php if(!empty($message)): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
        </div>
        <div class="flex">
            <div class="box">
                <span><?= $total_playlists; ?></span>
                <p>Total Playlists</p>
                <a href="playlists.php" class="btn">View Playlists</a>
            </div>
            <div class="box">
                <span><?= $total_contents; ?></span>
                <p>Total Videos</p>
                <a href="contents.php" class="btn">View Content</a>
            </div>
            
            </div>
            <div class="box">
                <span><?= $total_comments; ?></span>
                <p>Total Comments</p>
                <a href="comments.php" class="btn">View Comments</a>
            </div>
        </div>
    </div>

</section>

<!-- Link to custom JavaScript file -->
<script src="../js/teacher_script.js"></script>

</body>
</html>
