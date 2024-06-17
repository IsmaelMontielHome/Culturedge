<div class="main">
  <h1>New Post</h1>
  <form action="/posts/create" method="post" enctype="multipart/form-data">
    <div class="box">
      <div class="user">
        <div class="user_card" >
          <div class="user_card-info">
          <?php if (isset($_SESSION["user"]["avatar"]) && $_SESSION["user"]["avatar"]): ?>
            <img src="/assets/imgs/<?= $_SESSION["user"]["avatar"];?>" class="user-card-img" alt="<?= $_SESSION["username"]; ?>" />
          <?php else: ?>
            <img src="/resources/img/user.png"class="user-card-img"   alt="Anna Smith" />
          <?php endif; ?>
          <div>
            <p class= "profile-card"><?= $_SESSION['user']['username']; ?></p>
            <p class="date"><?= date('Y/m/d') ?></p>
          </div>
        </div>
      </div>
      <div class="line"></div>
      <div class="text">
        <input type="hidden" name="user_id" value="<?= $_SESSION['user']['id']; ?>">
        <div class="field">
          <input type="text" name="title" id="title" placeholder="A example of a title" required>
        </div>
        <div class="field">
          <i class="bi bi-paperclip attach-file" onclick="$('#images').click()"></i>
          <textarea name="description" id="description" cols="30" rows="10"
            placeholder="Your thoughts go here..."></textarea>
        </div>
        <div class="field">
          <select name="unesco_theme_id" id="unesco_theme_id" data-theme="" required>
            <option value="" disabled selected>Select a theme</option>
          </select>
        </div>
        <div class="field" id="files-container">
          <input type="file" name="images[]" id="images" onchange="uploadedImage(event, 0)"
            accept="image/jpg, image/jpeg, image/png, image/gif" multiple>
        </div>
      </div>
      <div id="myModal-0" class="modal">
        <div class="modal-content">
          <br><br><br><br>
          <div class="carousel-container" id="carouselContainer-0">
          </div>
        </div>
      </div>
      <div class="line"></div>
    </div>
    <div class="btn-container">
      <button type="submit" class="btn">Create</button>
    </div>
  </form>
</div>
<div class="back">
  <a onclick="window.history.back();"><i class='bx bx-arrow-back' ></i></a>
</div>
<script src="/resources/js/post.js"></script>
