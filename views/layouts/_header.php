<header>
  <?php $uri = filter_input_array(INPUT_GET)['uri'];
    if (!str_contains($uri, 'users')): ?>
    <a onclick="toggleHideNav(event)">
      <i class='bx bx-menu menu' id="menu-icon" ></i>
    </a>
  <?php endif; ?>

  <?php $is_admin_view = str_contains($uri, 'admins');
    if (!$is_admin_view): ?>
    <a href="/" class="logo">
      <img src="/resources/img/logo.png" alt="logo">
      <h2>CulturEdge</h2>
    </a>
    <div class="seeker">
      <button id="search-button"><i class='bx bx-search-alt'></i></button>
      <input class="cont" placeholder="Search" type="search" id="search-input" oninput="debounce(searchOnInput(), 300)">
    </div>
    <?php else: ?>
    <a href="/admins/console" class="logo">
      <img src="/resources/img/logo.png" alt="logo">
      <h2>CulturEdge</h2>
    </a>
  <?php endif; ?>
  
  <div class="actions">
    <?php if(isset($_SESSION['user'])): ?>
      <?php if($_SESSION['user']['rol'] === 0): ?>
        <a class="btn-console" href="<?= $is_admin_view ? '/' : '/admins/console' ?>">
        <i class="bi bi-eye-fill"></i><?= $is_admin_view ? 'USER VIEW' : 'ADMINISTRATOR' ?>
        </a>
      <?php endif; ?>

      <?php if (!$is_admin_view): ?>
        <button class="btn-notifications" onclick="app.userNotificationsOpen()">
          <i class='notifications-icon bx bx-bell'></i>
          <span class="notifications-count">
            <span class="spinner spinner-red"></span>
          </span>
        </button>
      <?php endif; ?>

      <button class="btn-userMenu" onclick="app.userMenuOpen()">
        <?php if (isset($_SESSION["user"]["avatar"]) && $_SESSION["user"]["avatar"]): ?>
          <img src="/assets/imgs/<?= $_SESSION["user"]["avatar"];?>" alt="<?= $_SESSION["username"]; ?>" />
        <?php else: ?>
          <img src="/resources/img/user.png" alt="Anna Smith" />
        <?php endif; ?>
      </button>
    <?php else: ?>
      <div class="login">
        <a href="/sessions/new">Sign up</a>
      </div>
    <?php endif; ?>
  </div>
</header>
