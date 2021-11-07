<?php
include "./config.php";

redirectIsNotAdmin();

if(isset($_POST) && ! empty($_POST) && isset($_POST['id'])) {
    redirectNotFound();
}

$circuit = getDatabase()->query('SELECT * FROM circuits WHERE id = '.$_POST['id'])->fetch();

if (! $circuit) {
    redirectNotFound();
}

$query = getDatabase()->query('DELETE FROM circuits WHERE id = '.$_POST['id']);
if ($query->execute()) {
    $_SESSION['success'] = 'Suppression du circuit effectuee avec succes !';
} else {
    $_SESSION['error'] = 'Une erreur est survenue lors de la suppression du circuit !';
}

header('Location: circuits_list.php');
