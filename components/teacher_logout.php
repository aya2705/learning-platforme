<?php

// Inclut le fichier de connexion à la base de données
include 'connect.php';

// Supprime le cookie 'tutor_id' en définissant sa durée de vie à une valeur passée (dans ce cas, time() - 1)
setcookie('tutor_id', '', time() - 1, '/');

// Redirige l'utilisateur vers la page d'accueil après la déconnexion
header('location:../home.php');

?>
