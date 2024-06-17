<h1>
  <i class='bx bxs-star'></i>
  Popular posts
</h1>
<div class="main">
  <?php foreach ($data as $d): ?>
    <div class="hoverbox">
    <div id="search-results" class="hoverbox-result"></div>
      <div class="box" id="results-list">
        <div class="user_card">
          <a href="/users/show/id:<?= $d["user_id"]; ?>" class="user_card-info">
            <img src="/resources/img/user.png" alt="user" class="user-card-img">
            <div>
              <p class="profile-card"><?= substr($d["username"], 0, 10) ?></p>
              <p class="date"><?= $d["created_at"] ?></p>
            </div>
          </a>
          <p class="user_card-post_theme">
            <a href="#">
              <i class="<?= $d["theme_icon"] ?>"></i>
              <?= $d["theme"] ?>
            </a>
          </p>
        </div>
        <div class="line"></div>
        <div class="vi">
          <a href="/posts/show/id:<?= $d["id"]; ?>"> 
            <div id="results-list-<?= $d["id"]; ?>">
              <div class="text">
                <h2><?= $d["title"] ?></h2>
                <p class="text-description"><?= $d["description"] ?></p>
              </div>
            </div>
          </a>
        </div>
        <div class="image-preview">
          <?php if (count($d["images"]) > 0): ?>
            <div class="image">
              <img src="/assets/imgs/<?= $d["images"][0]["image"] ?>" alt='Image from "<?= $d["title"] ?>"' onclick="openModal(<?= $d['id'] ?>)">
            </div>
            <?php if (count($d["images"]) > 1): ?>
              <div class="image-overlay" onclick="openModal(<?= $d['id'] ?>)">
                +<?= count($d["images"]) - 1 ?>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div> 
        <div>
          <div class="actions">
            <div class="info">
              <a href="/posts/show/id:<?= $d["id"]; ?>">
                <p class="likes <?= isset($_SESSION['user']) ? '' : 'openModal' ?>" id="reactions-count-<?= $d['id'] ?>">
                  <img src="/resources/img/like.png" alt="like"> <?= $d['total_reactions'] ?> reactions 
                </p>
              </a>
            </div>
            <div class="info">
              <a href="/posts/show/id:<?= $d["id"]; ?>">
                <p><?= $d['total_comments'] ?? 0 ?> comments</p>
              </a>
            </div>
          </div>
        </div>
        <?php if(isset($_SESSION['user']['id'])): ?>
          <div class="all-reaction" id="react_<?php echo $d["id"]?>">
            <img src="/resources/img/thumb.gif" class="reaction" id="thumb_<?php echo $d["id"]?>"> 
            <img src="/resources/img/haha.gif" class="reaction" id="haha_<?php echo $d["id"]?>">
            <img src="/resources/img/love.gif" class="reaction" id="love_<?php echo $d["id"]?>">
            <img src="/resources/img/wow.gif" class="reaction" id="wow_<?php echo $d["id"]?>">
            <img src="/resources/img/sad.gif" class="reaction" id="sad_<?php echo $d["id"]?>">
            <img src="/resources/img/angry.gif" class="reaction" id="angry_<?php echo $d["id"]?>">
          </div>
        <?php endif; ?>
        <div class="line"></div>
        <div class="actions">
          <div class="react-con" align="center" id="<?php echo $d["id"];?>">
            <?php if (isset($_SESSION['user']['id']) && !empty($d['user_reactions'])): ?>
                <img src="/resources/img/<?= $d['user_reactions'];?>.png" class="reaction-selected">
            <?php else: ?>
              <p><i class='bx bxs-like' onclick='app.checkSession()'></i></p>
            <?php endif; ?>
          </div>
          <a href="/posts/show/id:<?= $d["id"]; ?>">
            <p><i class='bx bxs-chat'></i> Comment</p>
          </a>
        </div>
      </div>
    </div>

    <div id="myModal-<?= $d["id"] ?>" class="modal">
      <span class="close" onclick="closeModal(<?= $d['id'] ?>)">&times;</span>
      <div class="modal-content">
        <br><br><br><br>
        <div class="carousel-container" id="carouselContainer-<?= $d['id'] ?>">
          <?php foreach ($d["images"] as $image): ?>
            <div class="carousel-slide">
              <img src="/assets/imgs/<?= $image["image"] ?>" class="carousel-image" alt='Image from "<?= $d['title'] ?>"'>
            </div>
          <?php endforeach; ?>
        </div>
        <?php if (count($d["images"]) > 1): ?>
          <a class="prev" onclick="changeSlide(-1, <?= $d['id'] ?>)">&#10094;</a>
          <a class="next" onclick="changeSlide(1, <?= $d['id'] ?>)">&#10095;</a>
        <?php endif; ?>
      </div>
    </div>

    <?php endforeach; ?>
</div>

<div id="modal" class="modal-container">
    <div class="modal-header">
        <i class='bx bx-error'></i>
        <h2>You have to log in</h2>
    </div>
    <span class="close-modal" onclick="closeModal()">&times;</span>
</div>

<div class="not-found" style="display: none;">
    <p>No se encontraron resultados.</p>
</div>

<script src="/resources/js/reaction.js"></script>
<script src="/resources/js/search.js"></script>


