<div class="return">
  <a href="/"><i class='bx bx-arrow-back'></i></a>
</div>
<div class="showpost">
<div id="search-results" class="hoverbox-result"></div>
  <div class="show">
    <div class="user_card">
      <a href="/users/show/id:<?= $data["user_id"]; ?>" class="user_card-info">
        <img src="/resources/img/user.png" alt="user" class="user-card-img">
        <div>
          <p class="profile-card"><?= substr($data["username"], 0, 10) ?></p>
          <p class="date"><?= $data["created_at"] ?></p>
        </div>
      </a>
      <p class="user_card-post_theme">
        <a href="#">
          <i class="<?= $data["theme_icon"] ?>"></i>
          <?= $data["theme"] ?>
        </a>
      </p>
    </div>
    <div class="line"></div>
    <div>
      <h2><?= $data["title"] ?></h2>
      <p><?= $data["description"] ?></p>
      <div class="images" onclick="openShowModal(<?= $data['id'] ?>)">
        <?php if (!empty($data["images"])): ?>
          <div class="image">
            <img src="/assets/imgs/<?= $data["images"][0]["image"] ?>" alt='Image from "<?= $data["title"] ?>"'>
            <?php if (count($data["images"]) > 1): ?>
              <div class="image-overlay">+<?= count($data["images"]) - 1 ?></div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
      <?php if(isset($_SESSION['user']['id'])): ?>
        <div class="all-reaction" id="react_<?php echo $data["id"]?>">
          <img src="/resources/img/thumb.gif" class="reaction" id="thumb_<?php echo $data["id"]?>"> 
          <img src="/resources/img/haha.gif" class="reaction" id="haha_<?php echo $data["id"]?>">
          <img src="/resources/img/love.gif" class="reaction" id="love_<?php echo $data["id"]?>">
          <img src="/resources/img/wow.gif" class="reaction" id="wow_<?php echo $data["id"]?>">
          <img src="/resources/img/sad.gif" class="reaction" id="sad_<?php echo $data["id"]?>">
          <img src="/resources/img/angry.gif" class="reaction" id="angry_<?php echo $data["id"]?>">
        </div>
      <?php endif; ?>
      <div class="actions-show">
        <div class="react-con" align="center" id="<?php echo $data["id"];?>">
          <?php if ($data['total_reactions'] > 0 || isset($_SESSION['user']['id'])): ?>
            <?php if (isset($_SESSION['user']['id']) && !empty($data['user_reactions'])): ?>
              <img src="/resources/img/<?php echo $data['user_reactions'];?>.png" class="reaction-selected">
            <?php else: ?>
              <p class="like-action"><i class='bx bxs-like' onclick='app.checkSession()'></i></p>
            <?php endif; ?>
          <?php else: ?>
              <p class="like-action"><i class='bx bxs-like' onclick='app.checkSession()'></i></p>
          <?php endif; ?>
        </div>
        <i class="likes <?= isset($_SESSION['user']) ? '' : 'openModal' ?>" id="reactions-count-<?= $data['id'] ?>">
          reactions: <?= $data['total_reactions'] ?? 0 ?>
        </i>
      </div>
    </div>
  </div>
</div>

<div id="showModal-<?= $data["id"] ?>" class="show-modal">
  <span class="show-close" onclick="closeShowModal(<?= $data["id"] ?>)">&times;</span>
  <br><br><br><br>
  <div class="show-modal-content">
    <div class="show-carousel-container" id="showCarouselContainer-<?= $data["id"] ?>">
      <?php foreach ($data["images"] as $image): ?>
        <div class="show-carousel-slide">
          <img src="/assets/imgs/<?= $image["image"] ?>" class="show-carousel-image" alt='Image from "<?= $data["title"] ?>"'>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (count($data["images"]) > 1): ?>
      <a class="show-prev" onclick="changeShowSlide(-1, <?= $data['id'] ?>)">&#10094;</a>
      <a class="show-next" onclick="changeShowSlide(1, <?= $data['id'] ?>)">&#10095;</a>
    <?php endif; ?>
  </div>
</div>
<!-- Comments Section -->
<div class="main_comments">
  <div class="count_comments">
    <i class='bx bxs-chat'></i>
    <p><?= $d['total_comments'] ?? 0 ?> comments</p>
    <button><i class='bx bx-dots-horizontal-rounded'></i></button>
  </div>
  <div class="line"></div>
  <div class="all_comments">
    <div class="user_card">
      <i class='bx bxs-user-voice'></i>
      <div class="data">
        <h3>Comments</h3>
      </div>
    </div>
    <?php if (isset($_SESSION['user'])): ?>
      <form action="/posts/create_comment_father" method="post">
        <div class="comment-user">
          <input type="hidden" name="user_id" value="<?= $_SESSION['user']['id']; ?>">
          <input type="hidden" name="post_id" value="<?= $data["id"]; ?>">
          <textarea name="comment" placeholder="Write Comment" oninput="autoSize(this)" rows="1"></textarea>
          <button type="submit">
            <i class='bx bxs-paper-plane'></i>
          </button>
        </div>
      </form>
    <?php else: ?>
      <p>Please log in to leave a comment</p>
    <?php endif; ?>
  </div>
  <br>
  <p id="comments"></p>
  <br>
</div>


<script src="/resources/js/comments.js"></script>
<script src="/resources/js/reaction.js"></script>
<script src="/resources/js/carousel.js"></script>