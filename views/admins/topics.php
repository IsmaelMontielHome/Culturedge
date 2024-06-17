<div class="body_wrapper">
  <div class="table-sector">
    <div class="header">
      <h1>TOPICS</h1>
      <div class="menu-user">
        <button class="buttonGreen" type="button" onclick="topics.create(event)">
          <i class="bi bi-plus-lg icon-button"></i> Add topics
        </button>
      </div>
    </div>
    <table class="content-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Topics</th>
          <th>Icon</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $index = 1 ?>
        <?php foreach ($data->data as $row): ?>
          <tr>
            <td><?= $index++; ?></td>
            <td><?= $row->theme; ?></td>
            <td><i class="<?= $row->icon; ?>"></i></td>
            <td><?= empty($row->updated_at) ? $row->created_at : "$row->updated_at edited" ?></td>
            <td class="content-table-actions">
              <button class="buttonBlue" type="button" onclick="topics.edit(event, <?= $row->id ?>)">
                <i class='bx bxs-edit'></i>
              </button>
              <button class="buttonRed" type="button" onclick="topics.delete(event, <?= $row->id ?>)">
                <i class='bx bxs-trash' ></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?> 
      </tbody>
    </table>
  </div>
</div>

<script src="/resources/js/topics.js"></script>
