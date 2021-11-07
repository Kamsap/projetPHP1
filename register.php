<?php
include('./config.php');

redirectForAuth();

$errors = [];
$values = [];
if(isset($_POST) && !empty($_POST)) {
    $datas = array_map('trim', $_POST);
    $datas = array_map('htmlentities', $datas);
    $datas = array_map('strip_tags', $datas);
    $datas = array_map('stripslashes', $datas);
    $datas = array_map('htmlspecialchars', $datas);

    if (! isset($datas['nom']) || empty($datas['nom'])) {
        $errors['nom'] = 'Le champ `nom` est obligatoire';
    }

    if (! isset($datas['prenom']) || empty($datas['prenom'])) {
        $errors['prenom'] = 'Le champ `prenom` est obligatoire';
    }

    if (! isset($datas['email']) || empty($datas['email'])) {
        $errors['email'] = 'Le champ `email` est obligatoire';
    }

    if (! isset($datas['password']) || empty($datas['password'])) {
        $errors['password'] = 'Le champ `mot de passe` est obligatoire';
    }

    if (! in_array('email', $errors)) {
        if (! filter_var($datas['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email est invalide.';
        } else {
            $query = getDatabase()->prepare('SELECT * FROM users WHERE email = ?');
            $query->execute([$datas['email']]);
            if ($query->fetch()) {
                $errors['email'] = 'L\'email n\'est plus disponible.';
            }
        }
    }

    if (empty($errors)) {
        $query = getDatabase()->prepare("INSERT INTO users (nom, prenom, email, password) 
            VALUES (:nom, :prenom, :email, :password)");
        $password = password_hash($datas['password'], PASSWORD_BCRYPT);
        $query->bindParam(':nom', $datas['nom']);
        $query->bindParam(':prenom', $datas['prenom']);
        $query->bindParam(':email', $datas['email']);
        $query->bindParam(':password', $password);
        if ($query->execute()) {
            $query = getDatabase()->prepare('SELECT * FROM users WHERE email = ?');
            $query->execute([$datas['email']]);
            $_SESSION['auth_user'] = (array) $query->fetch(PDO::FETCH_ASSOC);
            header('Location: index.php');
        } else {
            $errors['action'] = 'Une erreur innatendue s\'est produite';
        }

    }

    if (! empty($errors)) {
        $values['email'] = $datas['email'];
        $values['nom'] = $datas['nom'];
        $values['prenom'] = $datas['prenom'];
    }
}

include('./header.php');
?>

<div id="booking" class="section">
    <div class="section-center">
        <div class="container">
            <div class="row">
                <div class="col-md-7 mx-auto">
                    <div class="booking-form">
                        <div id="booking-cta">
                            <h1>Créez un compte</h1>
                            <p>
                                Créez un compte afin de profiter des services Vite Mon Vole
                                en toute simplicité.
                            </p>
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <span class="form-label">Nom</span>
                                        <input class="form-control" type="text" name="nom" value="<?= $values['nom'] ?? ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <span class="form-label">Prenom</span>
                                        <input class="form-control" type="text" name="prenom" value="<?= $values['prenom'] ?? ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <span class="form-label">E-mail</span>
                                        <input class="form-control" type="email" name="email" value="<?= $values['email'] ?? ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <span class="form-label">Mot de passe</span>
                                        <input class="form-control" type="password" name="password" required>
                                    </div>
                                </div>
                            </div>

                            <p>vous avez déjà un compte connectez-vous <span><a href="login.php">ici</a></span></p>
                            <div class="form-btn">
                                <button type="submit" name="submit" class="submit-btn">S'inscrire'</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include('./footer.php');
?>
