<?php

   // Inclusion du fichier de connexion à la base de données
   include 'connect.php';

   // Suppression du cookie 'user_id' en le définissant avec une date d'expiration passée
   setcookie('user_id', '', time() - 1, '/');

   // Redirection vers la page d'accueil
   header('location:../home.php');

?>
