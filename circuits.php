<?php
include('./config.php');

redirectIsNotAdmin();

$circuits = getCircuits();

include('header.php');
include('navbar.php');
?>
<div id="booking" class="section">
    <div class="container">
        <div class="row pt-4">
            <div class="col-md-12 card">
                <div class="card-header row">
                    <div class="ml-auto">
                        <a href="circuit_add.php" class="btn btn-primary">
                            <i class="fa fa-plus"></i>
                            Ajouter un circuit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-responsive w-full">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nom</th>
                            <th>Depart</th>
                            <th>Arrivee</th>
                            <th>Nombre d'escales</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($circuits as $circuit): ?>
                            <tr>
                                <td><?= $circuit->id; ?></td>
                                <td><?= $circuit->nom; ?></td>
                                <td><?= $circuit->nom; ?></td>
                                <td><?= $circuit->nom; ?></td>
                                <td><?= $circuit->nom; ?></td>
                                <td>
                                    <form action="circuit_edit.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $circuit->id; ?>">
                                        <?php if(! $circuit->is_admin): ?>
                                            <input type="hidden" name="set_admin" value="1">
                                        <?php endif; ?>
                                        <button class="btn <?= $circuit->is_admin ? 'btn-danger' : 'btn-success'; ?>">
                                            <i class="fa fa-pencil"></i>
                                            <?= $circuit->is_admin ? 'Unset admin role' : 'Set admin role'; ?>
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