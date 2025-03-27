<?php
// Vérifie si le tableau $message est défini
if(isset($message)){
   // Vérifie si $message est un tableau
   if(is_array($message)){
      // Parcourt chaque message dans le tableau $message
      foreach($message as $msg){
         // Affiche le message dans une boîte avec un bouton de fermeture
         echo '
         <div class="message">
            <span>'.$msg.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
}
?>

<header class="header">
   <section class="flex">
      <!-- Lien vers le tableau de bord avec un logo -->
      <a href="dashboard.php" class="logo">Teacher</a>

      <!-- Formulaire de recherche -->
      <form action="search_page.php" method="post" class="search-form">
         <input type="text" name="search" placeholder="search here..." required maxlength="100">
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>

      <!-- Icônes de menu, recherche et utilisateur -->
      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <!-- Profil de l'utilisateur connecté -->
      <div class="profile">
         <?php
            // Sélectionne le profil du tuteur basé sur l'ID
            $select_profile = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
            $select_profile->execute([$tutor_id]);
            // Vérifie si le profil existe
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <!-- Affiche l'image et le nom du tuteur -->
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= $fetch_profile['name']; ?></h3>
         <span>Teacher</span>
         <a href="profile.php" class="btn">view profile</a>
         <!-- Lien de déconnexion -->
         <a href="../components/teacher_logout.php" onclick="return confirm('logout from this website?');" class="delete-btn">logout</a>
         <?php
            } else {
         ?>
         <!-- Affiche le logo si aucun profil n'est trouvé -->
         <a href="home.php"><img src="uploaded_files/learninglogov2.png" style="width: 200px; height: 200px;"></a>
         <?php
            }
         ?>
      </div>
   </section>
</header>

<!-- Fin de la section d'en-tête -->

<!-- Début de la section de la barre latérale -->
<div class="side-bar">
   <div class="close-side-bar">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
         <?php
            // Sélectionne le profil du tuteur basé sur l'ID
            $select_profile = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
            $select_profile->execute([$tutor_id]);
            // Vérifie si le profil existe
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <!-- Affiche l'image et le nom du tuteur -->
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= $fetch_profile['name']; ?></h3>
         <span>Teacher</span>
         <a href="profile.php" class="btn">view profile</a>
         <?php
            } else {
         ?>
         <!-- Invite à se connecter ou s'inscrire si aucun profil n'est trouvé -->
         <h3>please login or register</h3>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div>
         <?php
            }
         ?>
      </div>

   <!-- Navigation de la barre latérale -->
   <nav class="navbar">
      <a href="dashboard.php"><i class="fas fa-home"></i><span>Home</span></a>
      <a href="playlists.php"><i class="fa-solid fa-bars-staggered"></i><span>Playlists</span></a>
      <a href="contents.php"><i class="fas fa-graduation-cap"></i><span>Contents</span></a>
      <a href="comments.php"><i class="fas fa-comment"></i><span>Comments</span></a>
      <a href="announcements.php"><i class="fa-solid fa-bullhorn"></i><span>Announcements</span></a>
      <a href="../components/teacher_logout.php" onclick="return confirm('logout from this website?');"><i class="fas fa-right-from-bracket"></i><span>logout</span></a>
   </nav>
</div>
<!-- Fin de la section de la barre latérale -->
