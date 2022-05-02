<?php

    if ( !isset($_GET['film_id']) || empty($_GET['film_id']) ) 
    {
        return header("Location: index.php");
    }

    $film_id = (int) strip_tags(trim($_GET['film_id']));

    require __DIR__ . "/db/connection.php";

    $req = $db->prepare("DELETE FROM film WHERE id=:id");
    $req->bindValue(":id", $film_id);
    $req->execute();
    $req->closeCursor();

    return header("Location: index.php");
