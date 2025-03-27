<?php
// Vérifie si la variable $message est définie
if(isset($message)){
    // Vérifie si $message est un tableau
    if (is_array($message)) { 
        // Parcourt chaque message dans le tableau $message
        foreach ($message as $msg) {
            // Affiche chaque message dans un div avec un bouton de fermeture
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
        <!-- Lien vers la page d'accueil -->
        <a href="home.php" class="logo"></a>

        <!-- Formulaire de recherche de cours -->
        <form action="search_course.php" method="post" class="search-form">
            <input type="text" name="search_course" placeholder="Search courses..." required maxlength="100">
            <button type="submit" class="fas fa-search" name="search_course_btn"></button>
        </form>

        <!-- Icônes pour le menu, la recherche et l'utilisateur -->
        <div class="icons">
            <div id="menu-btn" class="fa-solid fa-list"></div>
            <div id="search-btn" class="fa-solid fa-magnifying-glass"></div>
            <div id="user-btn" class="fa-solid fa-right-to-bracket"></div>
        </div>

        <!-- Section du profil utilisateur -->
        <div class="profile">
            <?php
            // Sélectionne les informations de l'utilisateur basé sur son ID
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            // Vérifie si l'utilisateur existe
            if($select_profile->rowCount() > 0){
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
                <h3><?= $fetch_profile['name']; ?></h3>
                <span>student</span>
                <a href="profile.php" class="btn" onclick="toggleNotifications()">View profile</a>
                <a href="components/user_logout.php" onclick="return confirm('logout from this website?');" class="delete-btn">Logout</a>
                <?php
            } else {
                ?>
                <h3>Please login or register</h3>
                <div class="flex-btn">
                    <a href="login.php" class="option-btn">Login</a>
                    <a href="register.php" class="option-btn">Register</a>
                </div>
                <?php
            }
            ?>
        </div>
    </section>
</header>

<!-- Début de la section de la barre latérale -->
<div class="side-bar">
    <div class="close-side-bar">
        <i class="fas fa-times"></i>
    </div>

    <div class="profile">
        <?php
        // Sélectionne le profil de l'utilisateur basé sur son ID
        $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
        $select_profile->execute([$user_id]);
        // Vérifie si l'utilisateur existe
        if($select_profile->rowCount() > 0){
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
            <h3><?= $fetch_profile['name']; ?></h3>
            <span>student</span>
            <a href="profile.php" class="btn" onclick="toggleNotifications()">view profile</a>
            <?php
        } else {
            // Si l'utilisateur n'existe pas, afficher le logo par défaut
            ?>
            <a href="home.php"><img src="uploaded_files/learninglogov2.png" style="width: 200px; height: 200px;"></a>
            <?php
        }
        ?>
    </div>

    <!-- Menu de navigation de la barre latérale -->
    <nav class="navbar">
        <a href="home.php"><i class="fa-solid fa-door-open"></i><span>Home</span></a>
        <a href="courses.php"><i class="fa-solid fa-book"></i><span>Courses</span></a>
        <a href="teachers.php"><i class="fa-solid fa-user-tie"></i><span>Teachers</span></a>
        <a href="announcements.php"><i class="fa-solid fa-bullhorn"></i><span>Announcements</span></a>
    </nav>
</div>
<!-- Fin de la section de la barre latérale -->

<!-- Début de la section des notifications -->
<div class="profile">
    <?php
    // Sélectionne les annonces actives, triées par date de création décroissante
    $statement = $conn->prepare("SELECT * FROM Announcements WHERE status = 'active' ORDER BY created_at DESC");
    $statement->execute();
    $announcements = $statement->fetchAll(PDO::FETCH_ASSOC);
    ?>
</div>
<!-- Fin de la section des notifications -->

</body>
</html>
