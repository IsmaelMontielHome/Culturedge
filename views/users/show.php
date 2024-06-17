<div class="header__wrapper">
  <?php $banner = empty($data->banner) ? '/resources/img/bg.jpeg' : "/assets/imgs/$data->banner"; ?>
  <header style="background-image: url('<?= $banner ?>')"><div id="search-results" class="hoverbox-result"></div></header>
  <div class="cols__container">
    <div class="left__col">
      <div class="img__container">
        <?php if ($data->avatar): ?>
          <img src="/assets/imgs/<?= $data->avatar ?>" alt="<?= $data->username ?>" />
        <?php else: ?>
          <img src="/resources/img/user.png" alt="Anna Smith"/>
        <?php endif; ?>
      </div>
      <p class="username"><?= $data->username ?></p>
      <p><?= $data->email ?></p>
      <?php if(isset($_SESSION['user'])): ?>
        <?php if ($data->id === $_SESSION['user']['id']): ?>
          <div class="edit-btn">
            <a onclick="user.myProfile(event)" class="btn-conf">
              <i class="bi bi-gear-fill sh-icon"></i> Edit Profile
            </a>
          </div>
        <?php endif; ?>
      <?php endif; ?>
      <ul class="about lists">
        <li><span><?= $data->followers ?></span>Followers</li>
        <li><span><?= $data->following ?></span>Following</li>
        <li><span><?= $data->posts ?></span>Post</li>
      </ul>
      <div class="contents" id="user-data">
        <div class="hobbies">
          <div class="info">
            <h3>About me</h3>
            <div class="lists">
              <li><i class="bi bi-calendar3 sh-icon"></i>Joined <?= $data->created_at ?></li>
              <li><i class='bx bxs-cake'></i>Birthdate: <?= $data->birthdate ?? 'Not specified' ?></li>
              <li><i class='bx bx-user'></i>Gender: <?= $data->gender ?? 'Not specified' ?></li>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="right__col">
      <div class="menu-user">
        <ul class="lists">
          <li><a class="option active" onclick="user.posts(event)">POSTS</a></li>
          <li><a class="option" onclick="user.media(event)">MEDIA</a></li>
          <li><a class="option" onclick="user.comments(event)">COMMENTS</a></li>
        </ul>
        <?php if ($data->follower): ?>
          <form onsubmit="user.unfollow(event, this)">
            <input type="hidden" name="user_id" value="<?= $data->id ?>">
            <input type="hidden" name="follower_id" value="<?= $_SESSION['user']['id'] ?>">
            <button type="submit">Unfollow</button>
          </form>
        <?php elseif (isset($_SESSION['user']) && $data->id !== $_SESSION['user']['id']): ?>
          <form onsubmit="user.follow(event, this)">
            <input type="hidden" name="user_id" value="<?= $data->id ?>">
            <input type="hidden" name="follower_id" value="<?= $_SESSION['user']['id'] ?>">
            <button type="submit">Follow</button>
          </form>
        <?php endif; ?>
        <?php if (isset($_SESSION['user']) && $data->id === $_SESSION['user']['id']): ?>
          <form>
            <a href="/posts/my_posts" class="action">
              Review
              <i class='bx bx-pencil'></i>
            </a>
            <a href="/posts/new" class="action">
              New Post
              <i class="bi bi-plus-circle"></i>
            </a>
          </form>
        <?php endif; ?>
      </div>

      <section id="mainSection" class="section">
      </section>
    </div>
  </div>
<script src="/resources/js/user.js"></script>
<script src="/resources/js/search.js"></script>

