<?php
include "./config.php";
redirectForAuth(false);

if (!isset($_GET['id']) || (isset($_GET['id']) && !filter_var($_GET['id'], FILTER_VALIDATE_INT))) {
    redirectNotFound();
}

$circuit = getDatabase()->query('SELECT * FROM circuits WHERE id = '.$_GET['id'])->fetch();

if (! $circuit) {
    redirectNotFound();
}

$query = getDatabase()->query('DELETE FROM circuits WHERE id = '.$_GET['id']);
$_SESSION['delete'] = $query->execute();

header('Location: circuits_list.php');
