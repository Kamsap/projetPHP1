<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="#">Vite Mon Vole</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Accueil</a>
                </li>
                <?php if (is_admin()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="users_list.php">Utilisateurs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="villes_list.php">Villes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="circuits.php">Circuits</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reservations.php">Réservations</a>
                </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="user_reservation.php">Mes réservations</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['auth_user'])) {?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            <?= $_SESSION["auth_user"]["email"]; ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="profil.php">
                                Modifier profil
                            </a>
                            <a class="dropdown-item" href="#">
                                Statut : <samp><?= $_SESSION["auth_user"]["is_admin"] ? "Admin" : "Utilisateur"; ?></samp>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">Se déconnecter</a>
                        </div>
                    </li>
                <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">S'inscrire</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Se connecter</a>
                        </li>
                <?php }; ?>
            </ul>
        </div>
    </div>
</nav>
