<?php
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];
}else{
    $user_id = '';
    header('location:login.php');
}

$select_user = $conn->prepare("SELECT name FROM users WHERE id = ?");
$select_user->execute([$user_id]);
$user = $select_user->fetch(PDO::FETCH_ASSOC);
$user_name = $user['name'];

$message = '';

if(isset($_POST['delete_account'])) {
    $insert_request = $conn->prepare("INSERT INTO deletion_requests (user_id, name) VALUES (?, ?)");
    $insert_request->execute([$user_id, $user_name]);
    
    // Vérifier si la demande a été acceptée ou refusée par l'administrateur
    $select_request_status = $conn->prepare("SELECT admin_id FROM deletion_requests WHERE user_id = ?");
    $select_request_status->execute([$user_id]);
    $request_status = $select_request_status->fetchColumn();
    
    if($request_status) {
        // Si la demande est acceptée, rediriger l'utilisateur vers la page register.php
        header('location: register.php');
        exit(); // Terminer le script pour éviter toute exécution supplémentaire
    } else {
        // Si la demande est toujours en attente ou a été refusée, vérifier si elle a été refusée par l'administrateur
        $select_request_status = $conn->prepare("SELECT admin_id FROM deletion_requests WHERE user_id = ?");
        $select_request_status->execute([$user_id]);
        $request_status = $select_request_status->fetchColumn();

        if(!$request_status) {
            // Si la demande a été refusée, le message de confirmation doit disparaître
            $message = '';
        } else {
            // Si la demande est toujours en attente, afficher le message
            $message = "Your deletion request has been sent to the administrator for review";
        }

        
    }
}
?>
