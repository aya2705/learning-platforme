<?php
// Inclusion du fichier de connexion à la base de données
include '../components/connect.php';

// Vérification si le cookie 'tutor_id' est défini, sinon redirection vers la page de connexion
if (!isset($_COOKIE['tutor_id'])) {
    header('location:login.php');
    exit();
}

// Récupération de l'identifiant du tuteur à partir du cookie
$tutor_id = $_COOKIE['tutor_id'];

// Préparation de la requête pour éviter les injections SQL, utilisation de PDO
$stmt = $conn->prepare("SELECT u.name, u.email, p.title AS playlist_title, c.comment FROM users u
                        INNER JOIN bookmark b ON u.id = b.user_id
                        INNER JOIN playlist p ON b.playlist_id = p.id
                        LEFT JOIN comments c ON u.id = c.user_id AND b.playlist_id = c.content_id
                        WHERE p.tutor_id = ?");
$stmt->bindParam(1, $tutor_id);
$stmt->execute();

// Sélection des contenus créés par le tuteur
$select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
$select_contents->execute([$tutor_id]);
$total_contents = $select_contents->rowCount();

// Sélection des playlists créées par le tuteur
$select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
$select_playlists->execute([$tutor_id]);
$total_playlists = $select_playlists->rowCount();

// Sélection des commentaires associés au tuteur
$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
$select_comments->execute([$tutor_id]);
$total_comments = $select_comments->rowCount();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Lien CDN pour Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <!-- Lien vers le fichier CSS personnalisé -->
    <link rel="stylesheet" href="../css/teacher_style.css">
</head>
<body>
    <!-- Inclusion de l'en-tête du tuteur -->
    <?php include '../components/teacher_header.php'; ?>
    <section class="dashboard">
        <h1 class="heading">Dashboard</h1>
        <h2 class="sub-heading">Statistics</h2>
        <!-- Tableau pour afficher les statistiques -->
        <table class="content-table">
            <thead>
                <tr>
                    <th class="table-content">Total Contents</th>
                    <th>Total Playlists</th>
                    <th>Total Comments</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $total_contents; ?></td>
                    <td><?= $total_playlists; ?></td>
                    <td><?= $total_comments; ?></td>
                </tr>
            </tbody>
        </table>

        <h2 class="sub-heading">Subscriptions</h2>
        <!-- Tableau pour afficher les abonnements -->
        <table class="content-table">
            <thead>
                <tr>
                    <th>Student's name</th>
                    <th>Email</th>
                    <th>Playlist's name</th>
                </tr>
            </thead>
            <tbody>
                <!-- Boucle pour afficher chaque utilisateur et ses informations -->
                <?php while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['playlist_title']) ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </section>
    <!-- Inclusion du script JavaScript -->
    <script src="../js/teacher_script.js"></script>
</body>
</html>
