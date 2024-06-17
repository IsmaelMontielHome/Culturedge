<script>
  function showWarningMessage() {
    alert("Your account will be deleted if not verified within 10 minutes. Proceed with caution.");
  }
</script>
<div class="user-view-container">
  <div class="left">
    <div class="returnForm">
      <a href="/sessions/new"><i class='bx bx-arrow-back comeback'></i></a>
    </div>
      <div class="principal2">
        <img src="/resources/img/login.gif" alt="login" class="img">
      </div>
      <p class="featured-words">
        <span>Your voice</span> matters. Join the community and share your ideas.
      </p>
  </div>
  <div class="right">
    <div class="datasign">
      <p>Have an account?</p>
      <a href="/sessions/new" class="new">Log in</a>
    </div>
    <div class="principal">
      <div class="slogan-reg">
        <span>REGISTER NOW!</span>
      </div>
      <div class="form-inputs">
        <form action="/registrations/create" method="POST" autocomplete="">
          <?php if (isset($_SESSION['error'])):
              $error_message = $_SESSION['error'];
              unset($_SESSION['error']); ?>
            <div id="error-alert" class="alert2">
              <i class="bi bi-info-circle icon-alert"></i>
              <p>Error: <?= $error_message; ?></p>
            </div>
          <?php endif; ?>
          <div class="user-input">
            <input class="input" type="text" name="username" placeholder=" " required>
            <label class="form_label">Username</label>
            <i class="bi bi-person icon"></i>
          </div>
          <div class="user-input">
            <input class="input" type="email" name="email" placeholder=" " id="email" required>
            <label class="form_label">Email</label>
            <i class="bi bi-envelope icon"></i>
          </div>
          <div class="user-input">
            <input type="password" class="input" name="password" placeholder=" " id="password" required>
            <label class="form_label">Password</label>
            <i class="bi bi-lock icon"></i>
            <p id="password_message" class="password-strength">Password is </p>
          </div>
          <div class="user-input">
            <input type="password" class="input" name="cpassword" placeholder=" " id="cpassword" required>
            <label class="form_label">Confirm password</label>
            <i class="bi bi-key icon"></i>
            <p id="confirm_message" class="password-strength">Password </p>
          </div>
          <div class="option2">
            <div class="btn-create">
              <input class="signup" type="submit" name="signup" value="Create New Account" onclick="showWarningMessage()" id="signup" disabled>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="/resources/js/form.js"></script>
<script>
  setTimeout(function(){
    var errorAlert = document.getElementById("error-alert");
    if (errorAlert) {
      errorAlert.style.display = "none";
    }
  }, 3000);
</script>
