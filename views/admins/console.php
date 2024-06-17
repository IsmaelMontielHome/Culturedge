<?php
$topThemes = $data->topThemes;
$totalComments = $data->totalComments;
$totalReactions = $data->totalReactions;
$totalUsers = $data->totalUsers;
$activeUsers = $data->activeUsers;
$totalThemes = $data->totalThemes;
$pendingPosts = $data->pendingPosts;
$temas = [];
$cantidadPosts = [];

foreach ($topThemes as $theme) {
    $temas[] = $theme->theme;
    $cantidadPosts[] = $theme->post_count;
}

$totalPublications = array_sum($cantidadPosts);
?>

<div class="body_wrapper">
  <div class="welcome-admin">
    <h1>WELCOME BACK, <?= $data->userName ?>!</h1>
    <p>Good to see you again!</p>
    <h6>Overview</h6>
  </div>
  <div class="general-data">
    <div class="boxed-data">
      <img src="/resources/img/icon-user.svg" alt="">
      <div>
        <p class="count5"><?php echo $totalUsers; ?></p>
        <p class="statistics">Total Users</p>
      </div>
    </div>
    <div class="boxed-data">
        <img src="/resources/img/icon-reaction.svg" alt="">
      <div>
        <p class="count4"> <?php echo $totalReactions; ?></p>
        <p class="statistics">Total Reactions</p>
      </div>
    </div>
    <div class="boxed-data">
        <img src="/resources/img/icon-post.svg" alt="">
        <div>
          <p class="count2"><?php echo $totalPublications; ?></p>
          <p class="statistics">Total Posts</p>
        </div>
    </div>
    <div class="boxed-data">
      <img src="/resources/img/icon-topic.svg" alt="">
      <div>
        <p class="count5"><?php echo $totalThemes; ?></p>
        <p class="statistics">Active Topics</p>
      </div>
    </div>
    <div class="boxed-data">
        <img src="/resources/img/icon-comment.svg" alt="">
      <div>
        <p class="count4"><?php echo $totalComments; ?></p>
        <p class="statistics">Total comments</p>
      </div>
    </div>
    <div class="boxed-data">
        <img src="/resources/img/icon-check.svg" alt="">
        <div>
          <p class="count2"><?php echo $activeUsers; ?></p>
          <p class="statistics">Active Users</p>
        </div>
    </div>
  </div>

  <div class="data-graphics">
    <div class="chart-container">
      <div class="graphics">
      <canvas id="myChart" data-temas='<?php echo json_encode($temas); ?>' data-cantidad-posts='<?php echo json_encode($cantidadPosts); ?>'></canvas>
        </canvas>
      </div>
    </div>
    <div class="chart-container">
      <div class="redirect-table">
        <h2>Review Pending Posts</h2>
        <a href="/admins/reviews">See more</a>
      </div>
      <table class="table-dashboard">
        <thead>
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Username</th>
              <th>Title</th>
            </tr>
        </thead>
        <tbody>
          <?php foreach ($pendingPosts as $post) { ?>
            <tr>
              <td><?php echo $post->id; ?></td>
              <td><?php echo date('d-m-Y', strtotime($post->created_at)); ?></td>
              <td><?php echo htmlspecialchars($post->username); ?></td>
              <td><?php echo htmlspecialchars($post->title); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/resources/js/dashboard.js"></script>
