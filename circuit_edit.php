<?php
include('./config.php');

redirectForAuth(false);

$user = $_SESSION['auth_user'];

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

    if (!isset($datas['email'])) {
        $errors['email'] = 'Le champ `email` est obligatoire';
    }

    if (! in_array('email', $errors)) {
        if (! filter_var($datas['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email est invalide.';
        } else {
            $query = getDatabase()->prepare('SELECT * FROM users WHERE id = ?');
            $query->execute([$user['id']]);
            $authUser = $query->fetch();
            if ($authUser->id != $user['id']) {
                $errors['email'] = 'L\'email n\'est plus disponible.';
            }
        }
    }

    if (empty($errors)) {
        $query = getDatabase()
            ->prepare('UPDATE users SET nom = :nom, prenom = :prenom, email = :email, password = :password WHERE id = ' . $user['id']);
        $password = ! isset($datas['password']) || empty($datas['password'])
            ? $user['password']
            : password_hash($datas['password'], PASSWORD_BCRYPT);
        $query->bindParam(':nom', $datas['nom']);
        $query->bindParam(':prenom', $datas['prenom']);
        $query->bindParam(':email', $datas['email']);
        $query->bindParam(':password', $password);
        if($query->execute()) {
            $values['success'] = 'La modification a ete faite avec succes';
            $user = (getDatabase()->query('SELECT * FROM users WHERE id = ' . $user['id']))
                ->fetch(PDO::FETCH_ASSOC);
        } else {
            $errors['action'] = 'Une erreur innatendue s\'est produite';
        }
    }
}

$values['nom'] = $user['nom'];
$values['prenom'] = $user['prenom'];
$values['email'] = $user['email'];


include('./header.php');
include('./navbar.php');
?>

<div class="d-flex" id="wrapper">
    <div id="page-content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Profile</h4>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <form method="POST" action="">
                                        <div class="form-group row">
                                            <label for="nom" class="col-4 col-form-label">Nom</label>
                                            <div class="col-8">
                                                <input id="nom" name="nom" value="<?= $user['nom']; ?>"
                                                    class="form-control here" type="text">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="prenom" class="col-4 col-form-label">Prenom</label>
                                            <div class="col-8">
                                                <input id="prenom" name="prenom" value="<?= $user['prenom']; ?>"
                                                    class="form-control here" type="text">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="lastname" class="col-4 col-form-label">Email</label>
                                            <div class="col-8">
                                                <input id="lastname" name="email" value="<?= $user['email']; ?>"
                                                    class="form-control here" type="email">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="text" class="col-4 col-form-label">Mot de passe</label>
                                            <div class="col-8">
                                                <input id="text" name="password" class="form-control here"
                                                    type="password" placeholder="*****">
                                                <small>Inserer un texte pour modifier mot de passe actuel</small>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="offset-4 col-8">
                                                <button name="submit" type="submit" class="btn btn-primary">
                                                    Modifier
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include('footer.php');
?>
