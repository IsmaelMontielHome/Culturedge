<div class="body_wrapper">
    <div class="header">
        <h1>USERS MANAGEMENT</h1>
    </div>
    <div class="section-admin">
        <h2>Admins</h2>
        <table class="content-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data->data as $row): ?>
                    <?php if ($row->rol == 0 && $row->id != $_SESSION['user']['id']):?>
                        <tr>
                            <td><?= $row->id; ?></td>
                            <td><?= $row->username; ?></td>
                            <td>
                                <?= $row->ban == 0 ? 'unban' : 'ban'; ?>
                            </td>
                            <td>admin</td>
                            <td class="content-table-actions">
                                <form method="POST" action="/admins/user_delete" onsubmit="return confirmDelete();">
                                    <input type="hidden" value="<?= $row->id; ?>" name="id" id="id-<?= $row->id; ?>">
                                    <button class="buttonBlack" type="submit">
                                        <span class="text"><i class='bx bxs-trash' ></i></span> <!-- delete -->
                                    </button>
                                </form>    
                                <form method="POST" action="/admins/user_ban">
                                    <input type="hidden" value="<?= $row->id; ?>" name="id" id="id-<?= $row->id; ?>">
                                    <button class="buttonRed" type="submit">
                                        <span class="text"><i class='bx bxs-user-x'></i></span> <!-- ban -->
                                    </button>
                                </form>
                                <form method="POST" action="/admins/user_unban">
                                    <input type="hidden" value="<?= $row->id; ?>" name="id" id="id-<?= $row->id; ?>">
                                    <button class="buttonGreen" type="submit">
                                        <span class="text"><i class='bx bxs-user-check'></i></span> <!-- unban -->
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?> 
            </tbody>
        </table>
    </div>
    <div class="section-admin">
        <h2>Users</h2>
        <table class="content-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data->data as $row): ?>
                    <?php if ($row->rol == 1 && $row->id != $_SESSION['user']['id']): // Users excluding current user ?>
                        <tr>
                            <td><?= $row->id; ?></td>
                            <td><?= $row->username; ?></td>
                            <td>
                                <?= $row->ban == 0 ? 'unban' : 'ban'; ?>
                            </td>
                            <td>user</td>
                            <td class="content-table-actions">
                                <form method="POST" action="/admins/user_delete" onsubmit="return confirmDelete();">
                                    <input type="hidden" value="<?= $row->id; ?>" name="id" id="id-<?= $row->id; ?>">
                                    <button class="buttonBlack" type="submit">
                                        <span class="text"><i class='bx bxs-trash' ></i></span>
                                    </button>
                                </form> 
                                <form method="POST" action="/admins/user_ban">
                                    <input type="hidden" value="<?= $row->id; ?>" name="id" id="id-<?= $row->id; ?>">
                                    <button class="buttonRed" type="submit">
                                        <span class="text"><i class='bx bxs-user-x'></i></span>
                                    </button>
                                </form>
                                <form method="POST" action="/admins/user_unban">
                                    <input type="hidden" value="<?= $row->id; ?>" name="id" id="id-<?= $row->id; ?>">
                                    <button class="buttonGreen" type="submit">
                                        <span class="text"><i class='bx bxs-user-check'></i></span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?> 
            </tbody>
        </table>
    </div>
    <script src="/resources/js/useradmin.js"></script>
</div>
