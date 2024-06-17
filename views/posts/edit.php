<div class="main">
  <h1>Edit Post</h1>
  <form action="/posts/patch" method="post" enctype="multipart/form-data">
    <div class="box">
      <div class="user_card" >
        <div class="user_card-info">
          <?php if (isset($_SESSION["user"]["avatar"]) && $_SESSION["user"]["avatar"]): ?>
            <img src="/assets/imgs/<?= $params["avatar"];?>" class="user-card-img" alt="<?= $_SESSION["username"]; ?>" />
          <?php else: ?>
            <img src="/resources/img/user.png" class="user-card-img"  alt="Anna Smith" />
          <?php endif; ?>
          <div>
            <p class= "profile-card"><?= $_SESSION['user']['username']; ?></p>
            <p class="date"><?= date('Y/m/d') ?></p>
          </div>
        </div>
      </div>
      <div class="line"></div>
      <div class="text">
        <input type="hidden" name="id" value="<?= $params["id"]; ?>">
        <div class="field">
          <input type="text" name="title" id="title" placeholder="Title" value="<?= $params["title"]; ?>" required>
        </div>
        <div class="field">
          <i class="bi bi-paperclip attach-file" onclick="$('#images').click()"></i>
          <textarea name="description" id="description" cols="30" rows="10"
            placeholder="Your thoughts go here..."><?= $params["description"]; ?></textarea>
        </div>
        <div class="field">
          <select name="unesco_theme_id" id="unesco_theme_id" data-theme="<?= $params["theme"]; ?>" required>
            <option value="" disabled selected>Select a theme</option>
          </select>
        </div>
        <div class="field" id="files-container">
          <input type="file" name="images[]" id="images" onchange="uploadedImage(event, <?= $params['id'] ?>)"
            accept="image/jpg, image/jpeg, image/png, image/gif" multiple>
          <?php $imageCount = count($params["images"]); ?>
          <?php if ($imageCount > 0): ?>
            <input type="button" class="btn" id="images_count" onclick="openModal(<?= $params['id'] ?>)"
              value="View Image<?= $imageCount > 1 ? "s +$imageCount" : '' ?>">
          <?php endif; ?>
        </div>
      </div>
      <div id="myModal-<?= $params["id"] ?>" class="modal">
        <div class="modal-content">
          <br><br><br><br>
          <div class="carousel-container" id="carouselContainer-<?= $params['id'] ?>">
            <?php foreach ($params["images"] as $image): ?>
              <div class="carousel-slide image-edit image-added" id="carouselSlide-<?= $image['id'] ?>">
                <img src="/assets/imgs/<?= $image["image"] ?>" class="carousel-image" alt='Image from "<?= $params['title'] ?>"'>
                <a class="remove-image" onclick="deleteImage(<?= $image['id'] ?>, <?= $params['id'] ?>)">
                  <span>&times;</span>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
          <?php if (count($params["images"]) > 1): ?>
            <a class="prev" onclick="changeSlide(-1, <?= $params['id'] ?>)">&#10094;</a>
            <a class="next" onclick="changeSlide(1, <?= $params['id'] ?>)">&#10095;</a>
          <?php endif; ?>
        </div>
      </div>
      <div class="line"></div>
    </div>
    <div class="btn-container">
      <button type="submit" class="btn">Edit</button>
    </div>
  </form>
</div>
<a href="/" class="return">
  <i class="bi bi-arrow-left"></i>
</a>
<script src="/resources/js/post.js"></script>

