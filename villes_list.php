<?php
include('./config.php');

redirectIsNotAdmin();

$villes = getVilles();

include('header.php');
include('navbar.php');
?>
<div id="booking" class="section">
    <div class="container">
        <div class="row pt-4">
            <div class="col-md-12 card">
                <div class="card-header row">
                    <div class="ml-auto">
                        <a href="ville_add.php" class="btn btn-primary">
                            <i class="fa fa-plus"></i>
                            Ajouter une ville
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-responsive w-full">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nom</th>
                            <th>Longitude</th>
                            <th>Latitude</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($villes as $ville): ?>
                            <tr>
                                <td><?= $ville->id; ?></td>
                                <td><?= $ville->nom; ?></td>
                                <td><?= $ville->longitude ?: ''; ?></td>
                                <td><?= $ville->latitude ?: ''; ?></td>
                                <td>
                                    <a href="ville_edit.php?id=<?= $ville->id; ?>" class="btn btn-primary">
                                        <i class="fa fa-pencil"></i>
                                        Editer
                                    </a>
                                    <form action="ville_delete.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $ville->id; ?>">
                                        <button class="btn btn-danger">
                                            <i class="fa fa-trash-old"></i>
                                            Supprimer
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