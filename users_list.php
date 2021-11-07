<?php
include('./config.php');

redirectIsNotAdmin();

$users = getUsers();

include('header.php');
include('navbar.php');
?>
<div id="booking" class="section">
    <div class="container">
        <div class="row pt-4">
            <div class="col-md-12 card">
                <div class="card-body">
                    <table class="table table-responsive w-full">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Noms et prenoms</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?= $user->id; ?></td>
                                <td><?= $user->nom . ' ' . $user->prenom; ?></td>
                                <td><?= $user->email; ?></td>
                                <td>
                                    <form action="user_edit.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $user->id; ?>">
                                        <?php if(! $user->is_admin): ?>
                                            <input type="hidden" name="set_admin" value="1">
                                        <?php endif; ?>
                                        <button class="btn <?= $user->is_admin ? 'btn-danger' : 'btn-success'; ?>">
                                            <i class="fa fa-pencil"></i>
                                            <?= $user->is_admin ? 'Unset admin role' : 'Set admin role'; ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include('footer.php');
?>
</body>

</html>