<?php
// Fichier download_content.php

// Inclusion du fichier de connexion à la base de données
include '../components/connect.php';

// Vérification si content_id est fourni
if(isset($_POST['content_id'])){
    $content_id = $_POST['content_id'];
    
    // Récupération des détails du contenu à partir de la base de données
    $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ?");
    $select_content->execute([$content_id]);
    $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

    // Chemin du fichier du contenu à télécharger
    $file_path = "../uploaded_files/" . $fetch_content['video'];

    // Définition des en-têtes pour le téléchargement du fichier
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
    header('Content-Length: ' . filesize($file_path));

    // Sortie du fichier pour le téléchargement
    readfile($file_path);
    exit();
} else {
    // Redirection si content_id n'est pas fourni
    header('Location: contents.php');
    exit();
}
?>
