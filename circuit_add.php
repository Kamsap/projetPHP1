<?php
include('./config.php');

redirectIsNotAdmin();

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

    if (!isset($datas['depart_id'])) {
        $errors['depart_id'] = 'Le champ `depart_id` est obligatoire';
    }

    if (!isset($datas['arrivee_id'])) {
        $errors['arrivee_id'] = 'Le champ `arrivee_id` est obligatoire';
    }

    if (!isset($datas['places'])) {
        $errors['places'] = 'Le champ `places` est obligatoire';
    }

    // Verifier si le nombre de place est bien un nombre et on verifie uniquement si le champ place existe
    if (!in_array('places', $errors) && filter_var($datas['places'], FILTER_VALIDATE_INT)) {
        $errors['places'] = 'Le champ `places` est incorrect';
    }

    if (empty($errors)) {
        $db = getDatabase();
        $query = $db->prepare("INSERT INTO circuits (nom, depart_id, arrivee_id, places) 
                VALUES (:nom, :depart_id, :arrivee_id, :places)");
        $query->bindParam(':nom', $datas['nom']);
        $query->bindParam(':depart_id', $datas['depart_id']);
        $query->bindParam(':arrivee_id', $datas['arrivee_id']);
        $query->bindParam(':places', $datas['places']);
        if ($query->execute()) {
            $datas['id'] = $db->lastInsertId();
        } else {
            $errors['action'] = 'Une erreur innatendue s\'est produite';
        }
    }

    if (!empty($errors)) {
        $values['arrivee_id'] = $datas['arrivee_id'];
        $values['depart_id'] = $datas['depart_id'];
        $values['nom'] = $datas['nom'];
        $values['places'] = $datas['places'];
    }
}

$villes = getVilles();

include('./header.php');
include('./navbar.php');
?>

<div id="booking" class="section">
    <div class="container">
        <div class="row pt-4">
            <div class="col-md-7 mx-auto">
                <div class="booking-form">
                    <div id="booking-cta">
                        <h1>Créer un circuit</h1>
                    </div>
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <span class="form-label">Nom</span>
                                    <input class="form-control" type="text" name="nom" value="<?= $values['nom'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <span class="form-label">Places</span>
                                    <input class="form-control" type="number" name="places" value="<?= $values['places'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <span class="form-label">Depart</span>
                                    <select class="form-control" name="depart_id" required>
                                        <option value="" <?= isset($values['depart_id']) && $values['depart_id'] > 0 ? '' : ' selected'; ?> disabled></option>
                                        <?php foreach($villes as $ville):?>
                                        <option value="<?= $ville->id; ?>"<?= isset($values['depart_id']) && $values['depart_id'] == $ville->id ? ' selected' : ''; ?>>
                                            <?= $ville->nom; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <span class="form-label">Arrivee</span>
                                    <select class="form-control" name="arrivee_id">
                                        <option value="" <?= isset($values['arrivee_id']) && $values['arrivee_id'] > 0 ? '' : ' selected'; ?> disabled></option>
                                        <?php foreach($villes as $ville):?>
                                        <option value="<?= $ville->id; ?>"<?= isset($values['arrivee_id']) && $values['arrivee_id'] == $ville->id ? ' selected' : ''; ?>>
                                            <?= $ville->nom; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
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
