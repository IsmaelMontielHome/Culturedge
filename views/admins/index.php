<div class="main">
    <table class="content-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Username</th>
                <th>Title</th>
                <th>Options</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <p class="profile-card"><p><?= $d["username"] ?></p> .</p>
            </tr>
            <?php
                array_multisort(array_column($data, 'created_at'), SORT_ASC, $data);
                foreach (array_slice($data, 0, 5) as $d): ?>
                <tr>
                    <td><?= $d["user_id"]; ?></td>
                    <td><?= date('d/m/Y', strtotime($d["created_at"])) ?></td>
                    <td><?= $d["username"] ?></td>
                    <td><?= $d["title"] ?></td>
                    <td class="content-table-actions">
                        <button class="buttonGreen" type="button">
                            <span class="text"><i class="bi bi-archive-fill"></i></span>
                        </button>
                        <button class="buttonRed" type="button">
                            <span class="text"><i class="bi bi-star-fill"></i></span>
                        </button>
                        <button class="buttonBlue" type="button">
                            <span class="text"><i class="bi bi-star-fill"></i></span>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?> 
        </tbody>
    </table>
    
</div>
