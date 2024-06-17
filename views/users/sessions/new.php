<div class="user-view-container">
  <div class="left">
  <div class="returnForm">
    <a href="/"><i class='bx bx-arrow-back comeback'></i></a>
  </div>
    <div class="principal2">
        <img src="/resources/img/login4.gif" alt="login" class="img">
    </div>
    <p class="featured-words">
      The next step towards a better tomorrow! <span>Join</span> and <span>Move</span> forward.
    </p>
  </div>
  <div class="right">
    <div class="returnForm not">
      <a href="/"><i class='bx bx-arrow-back comeback'></i></a>
    </div>
    <div class="register2">
      <p>Create an account</p>
      <a href="/registrations/new" class="new">Joined</a>
    </div>
    <div class="principal">
      <div class="slogan">
        <span>WELCOME BACK!</span>
      </div>
      <div class="form-inputs">
        <form action="/sessions/create/" method="POST" autocomplete="">
          <?php
          if (isset($_SESSION['error'])):
              $error_message = $_SESSION['error'];
              unset($_SESSION['error']); ?>
            <div id="error-alert" class="alert2">
              <i class="bi bi-info-circle icon-alert2"></i>
              <p><?= $error_message; ?></p>
            </div>
          <?php endif; ?>
          <div class="user-input">
            <input class="input" type="email" name="email" id="user-input" placeholder=" ">
            <label class="form_label">Email</label>
            <i class="bi bi-person icon" id="user-input"></i>
          </div> 
          <div class="user-input">
            <input class="input" type="password" name="password" id="user-input" placeholder=" ">
            <label class="form_label">Password</label>
            <i class="bi bi-lock icon" id="user-input"></i>
          </div>
          <div class="box-small">
            <a href="/passwords/new" class="forgot">Forgot your password?</a>
          </div>
          <div class="user-input">
            <input type="submit" value="SIGN IN" class="btn-sign">
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
if (isset($_SESSION['success'])) {
    echo '<div class="success-message">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
?>
<div id="errorMessages"></div>
