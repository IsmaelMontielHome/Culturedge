<div id="search-results" class="hoverbox-result"></div>
<div class="main">
  <div class="cols__container">
    <div class="right__col">
      <div class="menu-user">
        <ul class="lists">
          <li><a class="option active" onclick="myPost.review(event)">UNDER REVIEW</a></li>
          <li><a class="option" onclick="myPost.rejected(event)">REJECTED</a></li>
          <li><a class="option" onclick="myPost.accepted(event)">ACCEPTED</a></li>
        </ul>
        <?php if (isset($_SESSION['user']) && $data->id === $_SESSION['user']['id']): ?>

        <?php endif; ?>
      </div>

      <section id="mainSection" class="section">
      </section>
    </div>
  </div>
<script src="/resources/js/my_post.js"></script>
<script src="/resources/js/useradmin.js"></script>
<script src="/resources/js/search.js"></script>

