<?php
include('./config.php');

redirectIsNotAdmin();

if(isset($_POST) && ! empty($_POST) && isset($_POST['id'])) {
    $is_admin = isset($_POST['set_admin']) && $_POST['set_admin'] == 1;
    $query = getDatabase()
        ->prepare('UPDATE users SET is_admin = :is_admin WHERE id = ' . $_POST['id']);
    $query->bindParam(':is_admin', $is_admin);
    if($query->execute()) {
        $_SESSION['success'] = 'La modification a ete faite avec succes';
    } else {
        $_SESSION['error'] = 'Une erreur innatendue s\'est produite';
    }

    header('Location: users_list.php');
} else {
    header('Location: admin.php');
}
