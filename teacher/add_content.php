<?php
// Include the database connection file
include '../components/connect.php';

// Check if the 'tutor_id' cookie is set, if not redirect to the login page
if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location:login.php');
    exit(); // Ensure the script stops after redirection
}

// Check if the form is submitted
if (isset($_POST['submit'])) {

    // Generate a unique ID for the course
    $id = unique_id();
    $status = $_POST['status'];
    $status = filter_var($status, FILTER_SANITIZE_STRING); // Sanitize status
    $title = $_POST['title'];
    $title = filter_var($title, FILTER_SANITIZE_STRING); // Sanitize title
    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING); // Sanitize description
    $playlist = $_POST['playlist'];
    $playlist = filter_var($playlist, FILTER_SANITIZE_STRING); // Sanitize playlist
    $prerequisites = $_POST['prerequisites']; // Retrieve prerequisites

    // New variable to store keywords
    $keywords = '';

    // Check if keywords are entered
    if (isset($_POST['keywords'])) {
        $keywords = $_POST['keywords'];
    }

    // Sanitize keywords and separate by commas
    $keywords = filter_var($keywords, FILTER_SANITIZE_STRING);
    $keywordsArray = explode(',', $keywords);

    // Check if keywords are entered
    if (!empty($keywordsArray)) {
        // Concatenate keywords with commas
        $keywords = implode(',', $keywordsArray);
    }

    // Process the image (thumb)
    $thumb = $_FILES['thumb']['name'];
    $thumb = filter_var($thumb, FILTER_SANITIZE_STRING); // Sanitize file name
    $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
    $rename_thumb = unique_id() . '.' . $thumb_ext;
    $thumb_size = $_FILES['thumb']['size'];
    $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
    $thumb_folder = '../uploaded_files/' . $rename_thumb;

    // Process the video (or other document)
    $video = $_FILES['video']['name'];
    $video = filter_var($video, FILTER_SANITIZE_STRING); // Sanitize file name
    $video_ext = pathinfo($video, PATHINFO_EXTENSION);
    $rename_video = unique_id() . '.' . $video_ext;
    $video_tmp_name = $_FILES['video']['tmp_name'];
    $video_folder = '../uploaded_files/' . $rename_video;

    // Check file uploads (thumb and video)
    $thumb_uploaded = move_uploaded_file($thumb_tmp_name, $thumb_folder);
    $video_uploaded = move_uploaded_file($video_tmp_name, $video_folder);

    // Check required fields and insert data into the database
    if ($status !== '' && $title !== '') {
        $add_content = $conn->prepare("INSERT INTO `content`(id, tutor_id, playlist_id, title, description, video, thumb, status, prerequisites, keywords) VALUES(?,?,?,?,?,?,?,?,?,?)");
        $add_content->execute([$id, $tutor_id, $playlist, $title, $description, $rename_video, $rename_thumb, $status, $prerequisites, $keywords]);
        $message[] = 'New course uploaded!';
    } else {
        $message[] = 'Please fill in the required fields: Video Status, Video Title, and Playlist.';
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

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>

<body>

    <?php include '../components/teacher_header.php'; ?>

    <section class="video-form">
        <h1 class="heading">Upload Content</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <p>Course Status <span>*</span></p>
            <select name="status" class="box" required>
                <option value="" selected disabled>Status</option>
                <option value="active">Active</option>
                <option value="deactive">Deactivated</option>
            </select>
            <p>Course Title <span>*</span></p>
            <input type="text" name="title" maxlength="100" required placeholder="Enter the course title" class="box">
            <p>Prerequisites</p>
            <textarea name="prerequisites" class="box" placeholder="Enter the prerequisites" maxlength="1000" cols="30" rows="10"></textarea>
            <!-- Field for keywords -->
            <p>Keywords: (Separate multiple keywords with commas ,)</p>
            <input type="text" id="keywords" name="keywords" placeholder="Enter the keywords for the course" class="box" required>
            <p>Course Description</p>
            <textarea name="description" class="box" placeholder="Write the description" maxlength="1000" cols="30" rows="10"></textarea>
            <p>Course Playlist</p>
            <select name="playlist" class="box">
                <option value="" disabled selected>Select a playlist</option>
                <?php
                // Retrieve playlists for the current tutor_id
                $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
                $select_playlists->execute([$tutor_id]);
                if ($select_playlists->rowCount() > 0) {
                    while ($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?></option>
                <?php
                    }
                } else {
                    echo '<option value="" disabled>No playlist created yet!</option>';
                }
                ?>
            </select>
            <p>Select an Image</p>
            <input type="file" name="thumb" accept="image/*" class="box">
            <p>Select a Document (pdf/ppt/other)</p>
            <input type="file" name="video" accept="*" class="box">
            <input type="submit" value="Upload Document" name="submit" class="btn">
        </form>
        <!-- Display messages -->
        <?php if (isset($message)) : ?>
        <div class="message">
            <?php foreach ($message as $msg) : ?>
            <p><?= $msg; ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <script src="../js/teacher_script.js"></script>

</body>

</html>
