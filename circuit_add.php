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
    $fileContent = [];
    $coverContent = [];
    $dateYear = [];

    if (!isset($datas['nom'])) {
        $errors['nom'] = 'Le champ `nom du livre` est obligatoire';
    }

    if (!isset($datas['auteur'])) {
        $errors['auteur'] = 'Le champ `nom de l\'auteur` est obligatoire';
    }

    if (!isset($datas['description'])) {
        $errors['description'] = 'Le champ `description` est obligatoire';
    }

    if (!isset($datas['edition'])) {
        $errors['edition'] = 'Le champ `edition` est obligatoire';
    }

    if (!isset($datas['annee'])) {
        $errors['annee'] = 'Le champ `annee` est obligatoire';
    } elseif (isset($datas['annee']) && !empty($datas['annee'])) {
        $date = date_create_from_format('Y-m-d', $datas['annee']);
        if ($err = date_get_last_errors()) {
            if ($err['warning_count'] > 0) {
                $errors['annee'] = 'Ce champ `annee` est une date incorrecte !';
            } else {
                $dateYear['Y'] = $date->format('Y');
                $dateYear['m-Y'] = $date->format('m-Y');
            }
        }
    }

    if (isset($_FILES['file']) && isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        $filename = $_FILES['file']['name'];
        $filename_parts = explode('.', $filename);
        $file_ext = strtolower(end($filename_parts));

        if (!in_array($file_ext, array('pdf', 'ebook', 'docx'))) {
            $errors['file'] = 'Vous devez entrez un pdf ou ebook ou document valide comme fichier livre !';
        } else {
            $fileContent['ext'] = $file_ext;
            $fileContent['file_tmp'] = $_FILES['file']['tmp_name'];
            $fileContent['filename'] = $_FILES['file']['name'];
        }
    } else {
        $errors['file'] = 'Vous devez entrez un pdf ou ebook ou document valide comme fichier livre !';
    }

    if (isset($_FILES['cover']) && isset($_FILES['cover']['name']) && $_FILES['cover']['name'] != '') {
        $covername = $_FILES['cover']['name'];
        $covername_parts = explode('.', $covername);
        $cover_ext = strtolower(end($covername_parts));

        if (!in_array($cover_ext, array('jpg', 'jpeg', 'png', 'gif'))) {
            $errors['cover'] = 'Vous devez entrez une image valide comme photo de couverture !';
        } else {
            $coverContent['ext'] = $cover_ext;
            $coverContent['file_tmp'] = $_FILES['cover']['tmp_name'];
            $coverContent['filename'] = $_FILES['cover']['name'];
        }
    }

    if (empty($errors)) {
        $verification_required = true;
        $role = getRole('author');
        $auteur = [];
        $edition = [];
        if (isset($datas['auteur']) && filter_var($datas['auteur'], FILTER_VALIDATE_INT)) {
            $auteurQuery = getDatabase()->prepare("SELECT * FROM users WHERE id_user = ? && id_role_user = ?");
            $auteurQuery->execute([$datas['auteur'], $role->id_role]);
            $auteurRow = $auteurQuery->fetch();
            if($auteurRow) {
                $auteur['id'] = $auteurRow->id_user;
                $auteur['nom'] = $auteurRow->name_user;
            } else {
//                        $errors['action'] = 'Une erreur innatendue s\'est produite';
                $errors['auteur'] = 'L\'utilisateur selectionne n\'existe plus';
            }
        } else {
            $auteurQuery = getDatabase()->prepare("SELECT * FROM users WHERE name_user = ?");
            $auteurQuery->execute([$datas['auteur']]);
            $auteurRow = $auteurQuery->fetch();
            if($auteurRow) {
                $auteur['id'] = $auteurRow->id_user;
                $auteur['nom'] = $auteurRow->name_user;
            } else {
                $db = getDatabase();
                $auteurQuery = $db->prepare("INSERT INTO users (name_user, id_role_user) 
                        VALUES (:name_user, :id_role_user)");
                $auteurQuery->bindParam(':name_user', $datas['auteur']);
                $auteurQuery->bindParam(':id_role_user', $role->id_role);
                if ($auteurQuery->execute()) {
                    $auteur['id'] = $db->lastInsertId();
                    $auteur['nom'] = $datas['auteur'];
                    $verification_required = false;
                } else {
                    $errors['action'] = 'Une erreur innatendue s\'est produite';
                }
            }
        }

        if (isset($datas['edition']) && filter_var($datas['edition'], FILTER_VALIDATE_INT)) {
            $editionQuery = getDatabase()->prepare("SELECT * FROM editions WHERE id_edition = ?");
            $editionQuery->execute([$datas['edition']]);
            $editionRow = $editionQuery->fetch();
            if($editionRow) {
                $edition['id'] = $editionRow->id_edition;
                $edition['nom'] = $editionRow->name_edition;
            } else {
//                        $errors['action'] = 'Une erreur innatendue s\'est produite';
                $errors['edition'] = 'L\'utilisateur selectionne n\'existe plus';
            }
        } else {
            $editionQuery = getDatabase()->prepare("SELECT * FROM editions WHERE name_edition = ?");
            $editionQuery->execute([$datas['edition']]);
            $editionRow = $editionQuery->fetch();
            if($editionRow) {
                $edition['id'] = $editionRow->id_edition;
                $edition['nom'] = $editionRow->name_edition;
            } else {
                $db = getDatabase();
                $editionQuery = $db->prepare("INSERT INTO editions (name_edition) VALUES (:name_edition)");
                $editionQuery->bindParam(':name_edition', $datas['edition']);
                if($editionQuery->execute()) {
                    $edition['id'] = $db->lastInsertId();
                    $edition['nom'] = $datas['edition'];
                    $verification_required = false;
                } else {
                    $errors['action'] = 'Une erreur innatendue s\'est produite';
                }
            }
        }


        if ($verification_required && empty($errors)) {
            $query = getDatabase()->prepare('SELECT * FROM books
                    WHERE edited_at = ? && name_book = ? && id_edition_book = ? && id_author_book = ?');
            $query->execute([$datas['annee'], $datas['nom'], $datas['edition'], $datas['auteur']]);
            $result = $query->fetchAll();
            if (count($result) > 0) {
                $errors['action'] = "Ce livre est deja present dans notre base de donnees !";
            }
        }

        if (empty($errors) && !empty($fileContent)) {
            $fileName = $auteur['nom'].' - '.$datas['nom'].' - '.$edition['nom'].' - '.$dateYear['Y'].'.'.$fileContent['ext'];
            $fileLink = 'storage/books/' . $fileName;
//            $i = 0;
//            while (file_exists($fileLink)) {
//                $fileName = $i++ . $file;
//                $fileLink = 'storage/books/' . $fileName;
//            }
            if (move_uploaded_file($fileContent['file_tmp'], $fileLink)) {
                $datas['filename'] = $fileName;
            } else {
                $errors['file'] = 'Une erreur innatendue s\'est produite lors de l\'upload du fichier du livre !';
            }

            if (!empty($coverContent)) {
                $coverName = $auteur['nom'].' - '.$datas['nom'].' - '.$edition['nom'].' - '.$dateYear['Y'].'-cover.'.$coverContent['ext'];
                $coverLink = 'storage/covers/' . $coverName;
                if (move_uploaded_file($coverContent['file_tmp'], $coverLink)) {
                    $datas['couverture_book'] = $coverName;
                } else {
                    $errors['cover'] = 'Une erreur innatendue s\'est produite lors de l\'upload de la photo de couverture !';
                    //                $errors['cover'] = 'Une erreur innatendue s\'est produite lors de l\'upload de la photo de couverture (verifier si le dossier de destination existe vraiment) !';
                }
            }

            if (empty($errors)) {
                $couverture = $datas['couverture_book'] ?? null;
                $newQuery = getDatabase()->prepare('INSERT INTO books (name_book, description_book, couverture_book, filename, edited_at, id_edition_book, id_author_book) 
                    VALUES (:name_book, :description, :couverture_book, :file, :annee, :id_edition_book, :id_author_book)');
                $newQuery->bindParam(':name_book', $datas['nom']);
                $newQuery->bindParam(':file', $datas['filename']);
                $newQuery->bindParam(':annee', $datas['annee']);
                $newQuery->bindParam(':description', $datas['description']);
                $newQuery->bindParam(':id_edition_book', $edition['id']);
                $newQuery->bindParam(':id_author_book', $auteur['id']);
                $newQuery->bindParam(':couverture_book', $couverture);
                if($newQuery->execute()) {
                    $values['success'] = 'L\'ajout a ete fait avec succes';
                } else {
                    $errors['action'] = 'Une erreur innatendue s\'est produite';
                }
            }
        }
    }

    if (!empty($errors)) {
        $values['description'] = $datas['description'];
        $values['annee'] = $datas['annee'];
        $values['nom'] = $datas['nom'];
        if (isset($datas['auteur']) && filter_var($datas['auteur'], FILTER_VALIDATE_INT)) {
            $values['auteur'] = $datas['auteur'];
        }
        if (isset($datas['edition']) && filter_var($datas['edition'], FILTER_VALIDATE_INT)) {
            $values['edition'] = $datas['edition'];
        }
    }
}

$editions = getEditions();
$auteurs = getUsers('author');

include('./header.php');
?>

<div id="booking" class="section">
    <div class="section-center">
        <div class="container">
            <!-- <?php if(isset($_SESSION['response'])) {?>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?= $_SESSION['response'];?>
            </div>
            <?php } ?> -->
            <!-- <p>ffffffffff</p> -->
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
                        <form action="./controller/login-back.php" method="POST">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <span class="form-label">Nom</span>
                                        <input class="form-control" type="text" name="nom">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <span class="form-label">Prenom</span>
                                        <input class="form-control" type="text" name="prenom">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <span class="form-label">E-mail</span>
                                        <input class="form-control" type="text" name="mail">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <span class="form-label">Mot de passe</span>
                                        <input class="form-control" type="password" name="password">
                                    </div>
                                </div>
                            </div>

                            <p>vous avez déjà un compte connectez-vous <span><a href="login.php">ici</a></span></p>
                            <div class="form-btn">
                                <button type="submit" name="go" class="submit-btn">S'identifier</button>
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
