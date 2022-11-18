<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand navbar-nav" href="user/index">
          <img src="image/cv-logo.png" width="50" height="50" class="mt-1" alt="logo_jobijoba">
          <span class="p-3">
            <span style="color:#139898">J</span>
            <span style="color:#dc4f4f">o</span>
            <span style="color:#eab960">b</span>
            <span style="color:#c14bdb">i</span>
            <span style="color:#dc4f4f">J</span>
            <span style="color:#c14bdb">o</span>
            <span style="color:#139898">b</span>
            <span style="color:#eab960">a</span>
          </span>
        </a>

<!-- common to admin and user -->
<?php if ($user): ?>
        <div class="navbar-nav">
          <a class="nav-link p-3 ms-4" href="skill/user_skills">
            <img src="image/masterings.png" width="32" height="32" alt="masterings">
            Skills
          </a>

          <a class="nav-link p-3 ms-4" href="experience/index">
            <img src="image/experiences.png" width="32" height="32" alt="experiences">
            Experiences
          </a>

  <?php $user_connected = $user; ?>

  <!-- specific to admin -->
  <?php if($user->is_admin()): ?>
          <a class="nav-link p-3 ms-4" href="skill/skills">
            <img src="image/manage-skills.png" width="32" height="32" alt="manage-skills">
            Manage skills
          </a>
          
          <a class="nav-link p-3 ms-4" href="place/places">
            <img src="image/manage-places.png" width="32" height="32" alt="manage-places">
            Manage places
          </a>
          
          <a class="nav-link p-3 ms-4" href="user/users">
            <img src="image/manage-users.png" width="32" height="32" alt="manage-users">
            Manage users
          </a>
          <a class="nav-link p-3 ms-4" href="session/index">
            <!-- <img src="image/manage-users.png" width="32" height="32" alt="manage-users"> -->
            Session
          </a>
          <a class="nav-link p-3 ms-4" href="session1/index">
            <!-- <img src="image/manage-users.png" width="32" height="32" alt="manage-users"> -->
            Session1
          </a>
          <a class="nav-link p-3 ms-4" href="session2/index">
            <!-- <img src="image/manage-users.png" width="32" height="32" alt="manage-users"> -->
            Session2
          </a>
  <?php endif; ?>

          <!-- common to admin and user -->
          <a class="nav-link p-3 ms-4" href="user/profile">
            <img src="image/profile1.png" width="32" height="32" alt="profile_icon">
            <?= $user->fullname ?>
          </a>
          <a class="nav-link p-3 ms-4" href="user/signout">
            <img src="image/logout.png" width="32" height="32" alt="logout_icon">
            Logout
          </a>

        </div>
      </div>
    </nav>

<!-- Guest -->    
<?php else: ?>
      <div class="navbar-nav">
          <a class="nav-link p-3 ms-4" href="user/login">
            <img src="image/login.png" width="32" height="32" class="mt-1" alt="logo_jobijoba">
            Login
          </a>

          <a class="nav-link p-3 ms-4" href="user/signup">
            <img src="image/clipboard.png" width="32" height="32" class="mt-1" alt="logo_jobijoba">
            Signup
          </a>
      </div>
    </div>
  </nav>  
<?php endif;?>