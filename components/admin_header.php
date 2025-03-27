<?php
if(isset($message)){
   if(is_array($message)){

   foreach($message as $msg){
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

      <a href="dashboard.php" class="logo">Admin</a>

      

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
         
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
            $select_profile->execute([$tutor_id]);
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= $fetch_profile['name']; ?></h3>
         <a href="profile.php" class="btn">view profile</a>
         <div class="flex-btn">
            
         </div>
         <a href="../components/teacher_logout.php" onclick="return confirm('logout from this website?');" class="delete-btn">logout</a>
         <?php
            }else{
         ?>
         <h3>please login or register</h3>
          <div class="flex-btn">
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div>
         <?php
            }
         ?>
      </div>

   </section>

</header>

<!-- header section ends -->

<!-- side bar section starts  -->

<div class="side-bar">

   <div class="close-side-bar">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <?php
         // Get the admin profile based on the provided email
         $admin_email = "chioua.hiba1@gmail.com"; // Use any email here
         $select_admin_profile = $conn->prepare("SELECT * FROM `admin` WHERE email = ?");
         $select_admin_profile->execute([$admin_email]);
         $fetch_admin_profile = $select_admin_profile->fetch(PDO::FETCH_ASSOC);
      ?>
      <img src="../uploaded_files/<?= $fetch_admin_profile['image']; ?>" alt="">
      <h3><?= $fetch_admin_profile['name']; ?></h3>
      <span><?= $fetch_admin_profile['email']; ?></span>
   </div>

   <nav class="navbar">
      <a href="dashboard.php"><i class="fas fa-home"></i><span>Home</span></a>
      <a href="requests.php"><i class="fa-solid fa-bell"></i><span>Requests</span></i></a>
      <a href="../components/teacher_logout.php" onclick="return confirm('logout from this website?');"><i class="fas fa-right-from-bracket"></i><span>Logout</span></a>
   </nav>

</div>

<!-- side bar section ends -->
