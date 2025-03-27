<?php
include "../components/connect.php";

if (isset($_COOKIE["tutor_id"])) {
    $tutor_id = $_COOKIE["tutor_id"];
} else {
    $tutor_id = "";
    header("location:login.php");
}

// Récupérer l'ID de l'administrateur
$select_admin = $conn->query("SELECT id FROM admin LIMIT 1");
$admin_id = $select_admin->fetchColumn();

// Traitement de la demande d'acceptation ou de refus pour les utilisateurs
if (isset($_POST["accept_user"]) || isset($_POST["refuse_user"])) {
    $request_id = $_POST["request_id"];
    $action = isset($_POST["accept_user"])
        ? $_POST["accept_user"]
        : $_POST["refuse_user"];

    if ($action == "Accept") {
        $conn->beginTransaction();
        $delete_user = $conn->prepare(
            "DELETE FROM users WHERE id in (SELECT user_id FROM deletion_requests WHERE id = ?)"
        );
        $delete_user->execute([$request_id]);
        // Supprimer les commentaires de l'étudiant correspondant
        $delete_student_comments = $conn->prepare(
            "DELETE FROM comments WHERE user_id in (SELECT user_id FROM deletion_requests WHERE id = ?)"
        );
        $delete_student_comments->execute([$request_id]);

        $delete_student_request = $conn->prepare(
            "DELETE FROM deletion_requests WHERE id = ?"
        );
        $delete_student_request->execute([$request_id]);

        $conn->commit();
        // Redirect after successful deletion
        header("location: dashboard.php");
        exit();
    } elseif ($action == "Reject") {
        // Supprimer la demande de la table deletion_requests
        $delete_student_request = $conn->prepare(
            "DELETE FROM deletion_requests WHERE id = ?"
        );
        $delete_student_request->execute([$request_id]);

        // Rediriger pour éviter la soumission en double
        header("location: dashboard.php");
        exit();
    }
}

// Récupérer les demandes de suppression en attente pour les utilisateurs
$select_deletion_requests = $conn->query(
    "SELECT d.id AS request_id, u.name AS user_name FROM deletion_requests d LEFT JOIN users u ON d.user_id = u.id WHERE d.admin_id IS NULL"
);
$deletion_requests = $select_deletion_requests->fetchAll();

// Récupérer les demandes de suppression des tuteurs avec les noms des comptes associés
$select_deletion_requests_tutors = $conn->query(
    "SELECT dt.request_id, dt.tutor_id, dt.tutor_name, t.name AS tutor_name FROM deletion_tutors dt LEFT JOIN tutors t ON dt.tutor_id = t.id"
);
$deletion_requests_tutors = $select_deletion_requests_tutors->fetchAll();

if (isset($_POST["accept_tutor"]) || isset($_POST["refuse_tutor"])) {
    $request = $_POST["request"];
    $action = isset($_POST["accept_tutor"])
        ? $_POST["accept_tutor"]
        : $_POST["refuse_tutor"];

    if ($action == "Accept") {
        try {
            $conn->beginTransaction();

            // Supprimer le compte du tuteur correspondant
            $delete_tutor = $conn->prepare(
                "DELETE FROM tutors WHERE id = (SELECT tutor_id FROM deletion_tutors WHERE request_id = ?)"
            );
            $delete_tutor->execute([$request]);
            // Supprimer les cours du tuteur correspondant
            $delete_tutor_content = $conn->prepare(
                "DELETE FROM content WHERE tutor_id = (SELECT tutor_id FROM deletion_tutors WHERE request_id = ?)"
            );
            $delete_tutor_content->execute([$request]);
            // Supprimer les commentaires du tuteur correspondant
            $delete_tutor_comments = $conn->prepare(
                "DELETE FROM comments WHERE tutor_id = (SELECT tutor_id FROM deletion_tutors WHERE request_id = ?)"
            );
            $delete_tutor_comments->execute([$request]);
            // Supprimer les playlistes du tuteur correspondant
            $delete_tutor_content = $conn->prepare(
                "DELETE FROM playlist WHERE tutor_id = (SELECT tutor_id FROM deletion_tutors WHERE request_id = ?)"
            );
            $delete_tutor_content->execute([$request]);

            // Supprimer la demande de la table deletion_tutors
            $delete_request = $conn->prepare(
                "DELETE FROM deletion_tutors WHERE request_id = ?"
            );
            $delete_request->execute([$request]);

            $conn->commit();
            // Redirect after successful deletion
            header("location: dashboard.php");
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $message = "Error: " . $e->getMessage();
        }
    } elseif ($action == "Reject") {
        // Rejection logic
        // Supprimer la demande de la table deletion_tutors
        $delete_request = $conn->prepare(
            "DELETE FROM deletion_tutors WHERE request_id = ?"
        );
        $delete_request->execute([$request]);

        // Rediriger pour éviter la soumission en double
        header("location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests</title>
    <link rel="stylesheet" href="../css/admin_style.css">
     <!-- Importer des fonts de l'internet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body>
<?php include "../components/admin_header.php"; ?>
<div class="deletion-requests">
    <!-- affichage des demandes de suppression pour les etudiants -->
    <h1 class="attente"><center>Deletion requests for students</center></h1>
    <ul>
        <?php foreach ($deletion_requests as $request): ?>
            <li>Student <?= $request[
                "user_name"
            ] ?> <i>Waiting for his account to be deleted</i>
                <form action="" method="post">
                    <input type="hidden" name="request_id" value="<?= $request[
                        "request_id"
                    ] ?>">
                    <button type="submit" class="accept" name="accept_user" value="Accept">Accept</button>
                    <button type="submit" class="reject" name="refuse_user" value="Reject">Refuse</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<!-- affichage des demande de suppression pour les professeurs -->
<h1 class="attente"><center>Deletion requests for tutors</center></h1>
    <ul>
        <?php foreach ($deletion_requests_tutors as $dm): ?>
            <li> Teacher <?= $dm[
                "tutor_name"
            ] ?>  <i>Waiting for his account to be deleted</i>
            <form action="" method="post"> 
                    <input type="hidden" name="request" value="<?= $dm[
                        "request_id"
                    ] ?>"> 
                    <button type="submit" class="accept"  name="accept_tutor" value="Accept">Accept</button>
                    <button type="submit" class="reject" name="refuse_tutor" value="Reject">Refuse</button>
                </form>
          </li>
        <?php endforeach; ?>
    </ul>
    <!-- importer le script js -->
<script src="../js/admin_script.js"></script>
</body>
</html>
