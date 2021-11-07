<?php
include('./config.php');

redirectIsNotAdmin();

if (!isset($_GET['id']) || (isset($_GET['id']) && !filter_var($_GET['id'], FILTER_VALIDATE_INT))) {
    redirectNotFound();
}

$ville = getDatabase()->query('SELECT * FROM villes WHERE id = ' . $_GET['id'])->fetch();

if (! $ville) {
    redirectNotFound();
}

$errors = [];
$values = [];
if(isset($_POST) && !empty($_POST)) {
    $datas = array_map('trim', $_POST);
    $datas = array_map('htmlentities', $datas);
    $datas = array_map('strip_tags', $datas);
    $datas = array_map('stripslashes', $datas);
    $datas = array_map('htmlspecialchars', $datas);

    if (!isset($datas['nom'])) {
        $errors['nom'] = 'Le champ `nom` est obligatoire';
    }

    if (! in_array('nom', $errors)) {
        $query = getDatabase()->prepare('SELECT * FROM villes WHERE nom = ? AND id != ' . $_GET['id']);
        $query->execute([$datas['nom']]);
        if ($query->fetch()) {
            $errors['nom'] = 'Une ville porte deja ce nom.';
        }
    }

    if (empty($errors)) {
        $query = getDatabase()
            ->prepare("UPDATE villes SET nom = :nom, longitude = :longitude, latitude = :latitude WHERE id = " . $ville->id);
        $query->bindParam(':nom', $datas['nom']);
        $query->bindParam(':longitude', $datas['longitude']);
        $query->bindParam(':latitude', $datas['latitude']);
        if ($query->execute()) {
            $_SESSION['success'] = 'Ville modifiee avec succes !';
            header('Location: villes_list.php');
        } else {
            $errors['action'] = 'Une erreur innatendue s\'est produite';
        }
    }

}

$values['latitude'] = $ville->latitude;
$values['longitude'] = $ville->longitude;
$values['nom'] = $ville->nom;

include('./header.php');
include('./navbar.php');
?>

<div id="booking" class="section">
    <div class="container">
        <div class="row pt-4">
            <div class="col-md-7 mx-auto">
                <div class="booking-form">
                    <div id="booking-cta">
                        <h1>Créer une ville</h1>
                    </div>
                    <form action="" method="POST">
                        <div class="row">
                            <?php if (! empty($errors)): ?>
                                <div class="col-12 alert alert-danger">
                                    <ul>
                                        <?php foreach ($errors as $error): ?>
                                            <li>
                                                <?= $error; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <span class="form-label">Nom</span>
                                    <input class="form-control" type="text" name="nom" value="<?= $values['nom'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <span class="form-label">Longitude</span>
                                    <input class="form-control" type="text" name="longitude" value="<?= $values['longitude'] ?? ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <span class="form-label">Latitude</span>
                                    <input class="form-control" type="text" name="latitude" value="<?= $values['latitude'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-btn">
                            <button type="submit" name="submit" class="submit-btn">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include('./footer.php');
?>
