<?php

    /* Connexion à une base MySQL avec l'invocation de pilote */
    $dsn_db = 'mysql:dbname=dwwm1b_repertoire;host=localhost';
    $user_db = 'root';
    $password_db = '';

    try
    {
        $db = new PDO($dsn_db, $user_db, $password_db);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch( PDOException $e )
    {
        echo "Error: " . $e->getMessage();
    }

?>