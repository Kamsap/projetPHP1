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

    if (!isset($datas['email']) || !isset($datas['password'])) {
        $errors['action'] = 'Veuillez remplir tous les champs';
    }

    if (empty($errors)) {
        $query = getDatabase()->prepare('SELECT * FROM users WHERE email = ?');
        $query->execute([$datas['email']]);
        if($user = $query->fetch()) {
            if (password_verify($datas['password'], $user->password)) {
                $_SESSION['auth_user'] = (array) $user;
                header('Location: index.php');
            }
        }
        $errors['action'] = 'Ces informations d\'identification ne correspondent pas à nos enregistrements';
    }

    if (empty($errors)) {
        $errors['action'] = 'Une erreur innatendue s\'est produite';
    }

    $values['email'] = $datas['email'];
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
                            <h1>Se connecter</h1>
                            <p>
                                Connectez-vous à l'aide de votre compte Vite Mon Vole et accédez à nos services
                            </p>
                        </div>
                        <div>
                            <?php if (isset($errors['action'])): ?>
                                <div class="alert alert-danger">
                                    <?= $errors['action']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <span class="form-label">E-mail</span>
                                        <input class="form-control" type="text" name="email" value="<?= $values['email'] ?? ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <span class="form-label">Mot de passe</span>
                                        <input class="form-control" type="password" name="password" required>
                                    </div>
                                </div>
                            </div>

                            <p>Si vous avez déjà compte, merci de vous identifiez <span><a href="register.php">ici</a></span></p>
                            <div class="form-btn">
                                <button name="login" class="submit-btn">S'identifier</button>
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
