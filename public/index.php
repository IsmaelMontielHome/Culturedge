<?php 
include './../controllers/application_controller.php';

session_start();

get_model('router');

$router = new Router();
$controller = $router->controller;
$action = $router->action;

$special_controllers_body = ['sessions', 'confirmations', 'registrations', 'passwords'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CulturEdge</title>
  <!-- Favicon -->
  <link rel="icon" href="/resources/img/favicon.ico" type="image/x-icon">
  <!-- FONTS -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Almarai&family=Inter&family=Lato&family=Roboto+Slab&family=Rubik&family=Poppins&display=swap">
  <!-- ICONS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
  <link rel='stylesheet' href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>
  <!-- STYLESHEETS -->
  <link rel="stylesheet" href="/resources/stylesheets/main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap-grid.min.css">
  <!-- JAVASCRIPT -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.8/sweetalert2.all.js" integrity="sha512-mDHahYvyhRtp6zBGslYxaLlAiINPDDEoHDD7nDsHoLtua4To71lDTHjDL1bCoAE/Wq/I+7ONeFMpgr62i5yUzw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="/resources/js/app.js"></script>
  <script src="/resources/js/main.js"></script>
  <script>
    app.user.id = <?= isset($_SESSION['user']) ? $_SESSION['user']['id'] : 'null' ?>;
    app.user.username = "<?= isset($_SESSION['user']) ? $_SESSION['user']['username'] : '' ?>";
    app.user.email = "<?= isset($_SESSION['user']) ? $_SESSION['user']['email'] : '' ?>";
    app.user.created_at = "<?= isset($_SESSION['user']) ? $_SESSION['user']['created_at'] : '' ?>";
    app.user.avatar = "<?= isset($_SESSION['user']) ? $_SESSION['user']['avatar'] : '' ?>";
  </script>
</head>
<?php if (in_array($controller, $special_controllers_body)): ?>
  <body>
    <div class="user-view-wrapper">
      <?= $router->dispatch() ?>
    </div>
  </body>
<?php elseif ($controller === 'users'): ?>
  <body>
    <?= render_layout('header'); ?>

    <div class="main-container">
      <div class="header_wrapper">
        <?= $router->dispatch() ?>
      </div>
    </div>
  </body>
<?php else: ?>
  <body>
    <?= render_layout('header'); ?>

    <div class="main-container">
      <?php if ($controller == 'admins'): ?>
        <nav id="main-nav">
          <?= render_layout('sidebar_admin'); ?>
        </nav>
      <?php else: ?>
        <nav id="main-nav">
          <?= render_layout('sidebar_main'); ?>
        </nav>
      <?php endif; ?>

      <main>
        <?= $router->dispatch() ?>
      </main>
      
      <?php if ($controller == 'posts' && $action == 'index'): ?>
        <?= render_layout('sidebar_chats'); ?>
      <?php endif; ?>
    </div>
  </body>
  <script src="/resources/js/search.js"></script>
<?php endif; ?>
</html>
