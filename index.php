<?php
    
    require __DIR__ . "/db/connection.php";

    $req = $db->prepare("SELECT * FROM film");
    $req->execute();
    $row = $req->rowCount();

    $films = [];
    
    if ( $row > 0 ) 
    {
        $films = $req->fetchAll();
    }


    
    
?>

<!-- -------------------------------------View------------------------------  -->

<?php $title = "Liste des films"; ?>

<?php require __DIR__ . "/partials/head.php"; ?>

    <?php require __DIR__ . "/partials/nav.php"; ?>

    <!-- Start the specific content for this page -->
    <main class="container-fluid">

        <h1 class="text-center my-3">Liste des films</h1>
    
        <div class="d-flex justify-content-end align-items-center">
            <a href="create.php" class="btn btn-primary">Ajouter film</a>
        </div>

        <?php if(count($films) == 0) : ?>
            <p>Aucun film ajouté à la liste.</p>
        <?php else : ?>
            <div class="container">
                <?php foreach($films as $film) : ?>
                    <div class="card">
                        <p>Id du film : <?= $film['id'] ?></p>
                        <p>Nom du film : <?= $film['name'] ?></p>
                        <p>Le(s) acteur(s) : <?= $film['actors'] ?></p>
                        <p>La note du film : <?= $film['review'] ?></p>
                        <a href="edit.php?film_id=<?= $film['id'] ?>">Modifier</a> 
                        <a href="delete.php?film_id=<?= $film['id'] ?>">Supprimer</a>
                    </div>
                    <hr>
                <?php endforeach ?>
            </div>
        <?php endif ?>

    </main>
    <!-- End the specific content for this page -->

<?php require __DIR__ . "/partials/foot.php"; ?>