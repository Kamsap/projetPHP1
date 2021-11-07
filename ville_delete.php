<?php
include "./config.php";

redirectIsNotAdmin();

if(isset($_POST) && ! empty($_POST) && isset($_POST['id'])) {

    $ville = getDatabase()->query('SELECT * FROM villes WHERE id = '.$_POST['id'])->fetch();

    if (! $ville) {
        redirectNotFound();
    }

    $query = getDatabase()->query('DELETE FROM villes WHERE id = '.$_POST['id']);
    if ($query->execute()) {
        $_SESSION['success'] = 'Suppression de la ville effectuee avec succes !';
    } else {
        $_SESSION['error'] = 'Une erreur est survenue lors de la suppression de la ville !';
    }

    header('Location: villes_list.php');
} else {
    header('Location: admin.php');
}
