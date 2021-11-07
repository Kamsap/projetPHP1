<?php
if (session_status() === PHP_SESSION_NONE) {
    // Initialiser la session
    session_start();
}

const TITRE_SITE = 'Vite Mon Vole';

function getDatabase()
{
    $host = 'localhost';
    $dbname = 'projetPHP1';
    $username = 'root';
    $password = '';

    try {
        $pdo = new \PDO('mysql:host='. $host .';dbname='.$dbname, $username, $password);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    } catch (\PDOException $e) {
        echo 'Erreur provenant de la base de donnees. Error: ' . $e->getMessage();
        die();
    }
}

function redirectForAuth($ifIsConnect = true)
{
    if ($ifIsConnect && isset($_SESSION['auth_user'])) {
        header('Location: index.php');
    } elseif (!$ifIsConnect && !isset($_SESSION['auth_user'])) {
        header('Location: login.php');
    }
}

function redirectIsNotAdmin()
{
    if (! isset($_SESSION['auth_user'])) {
        header('Location: login.php');
    } elseif (! $_SESSION['auth_user']['is_admin']) {
        header('Location: index.php');
    }
}

function redirectNotFound() {
    header('HTTP/1.1 404 Not Found');
    include('404.php');
    exit();
}

function is_admin() {
    return isset($_SESSION['auth_user']) && $_SESSION['auth_user']['is_admin'];
}

function insertAdmin() {
    try {
        $query = getDatabase()->prepare("INSERT INTO `users` (`nom`, `prenom`, `email`, `password`, `is_admin`) 
            VALUES ('admin', NULL, 'admin@admin.com', '$2y$10$09JjvNIjabEjVxPkOaaU8OXJjRIyyn7r9mMpBdD1eM6V87wZez2ca', 1)");
        if ($query->execute()) {
            header('Location: login.php');
        }
    } catch (\PDOException $e) {
        if(isset($e->errorInfo) && isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
            $query = getDatabase()->prepare("UPDATE `users`
                SET password = '$2y$10$09JjvNIjabEjVxPkOaaU8OXJjRIyyn7r9mMpBdD1eM6V87wZez2ca', is_admin = 1 
                WHERE email = 'admin@admin.com' ");
            if ($query->execute()) {
                header('Location: login.php');
            }
        } else {
            echo $e->getMessage();
            die();
        }
    }
}

function getVilles()
{
    return getDatabase()->query('SELECT * FROM villes')->fetchAll();
}

function getCircuits()
{
    return getDatabase()->query('SELECT * FROM circuits')->fetchAll();

//    $circuits = ['circuit cle', '3ag circuit', 'ugobok'];
//
//    foreach ($circuits as $circuit) {
//        $query = getDatabase()->prepare('SELECT * FROM circuits WHERE name_circuit = ?');
//        $query->execute([$circuit]);
//        if(!($query->fetch())) {
//            $newQuery = getDatabase()->prepare('INSERT INTO circuits (name_circuit) VALUES (:circuit_name)');
//            $newQuery->bindParam(':circuit_name', $circuit);
//            $newQuery->execute();
//        }
//    }
}

function getUsers($only_user = false)
{

    $query = 'SELECT * FROM users';
    if (isset($_SESSION['auth_user']) && isset($_SESSION['auth_user']['id'])) {
        $query = 'SELECT * FROM users WHERE id != ' . $_SESSION['auth_user']['id'];
    }

    if ($only_user) {
        $query .= ' AND is_admin = false';
    }

    return (getDatabase()->query($query))->fetchAll();
}
