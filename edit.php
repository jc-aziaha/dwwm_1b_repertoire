<?php
session_start();

    // Si l'identifiant envoyé est vide,
    // On redirige l'utilisateur sur la page index.php
    if ( !isset($_GET['film_id']) || empty($_GET['film_id']) ) 
    {
        return header("Location: index.php");
    }

    
    /*
    * On récupère l'id
    * On se protège contre les failles de type XSS
    * On convertit l'identifin-ant en nombre entier
    */
    $film_id = (int) strip_tags(trim($_GET['film_id']));

    // Si l'identifiant est vide, on redirige l'utilisateur vers index.php
    if ( empty($film_id) ) 
    {
        return header("Location: index.php");
    }

    // On établit une nouvelle connexion avec la base de données
    require __DIR__ . "/db/connection.php";

    // On fait une requête pour sélectionner toutes les colonnes 
    // d'un seul enregistrement de la table "film" 
    $req = $db->prepare("SELECT * FROM film WHERE id=:id");
    $req->bindValue(":id", $film_id);
    $req->execute();

    // On récupère le nombre d'enregistrement
    $row = $req->rowCount();

    // Si ce nombre n'est pas égale à 1,
    // On redirige l'utilisateur vers la page de laquelle proviennent
    // les informations (index.php)
    if ( empty($row) || $row != 1 ) 
    {
        return header("Location: index.php");
    }

    // Dans le cas contraire
    $film = $req->fetch();

    if ( $_SERVER['REQUEST_METHOD'] === "POST" ) 
    {

        $post_clean = [];
        $errors     = [];

        foreach ($_POST as $key => $value) 
        {
            $post_clean[$key] = strip_tags(trim($value));
        }

        // Validation des données

        // 1) name Input
        if ( isset($post_clean['name']) ) 
        {
            if (empty($post_clean['name'])) 
            {
                $errors['name'] = "Le nom du film est obligatoire.";
            }
            else if( mb_strlen($post_clean['name']) > 255 )
            {
                $errors['name'] = "Le nom doit contenir au maximum 255 carcatères.";
            }
        }

        // 2) actors Input
        if ( isset($post_clean['actors']) ) 
        {
            if (empty($post_clean['actors'])) 
            {
                $errors['actors'] = "Le nom du ou des acteurs du film est obligatoire.";
            }
            else if( mb_strlen($post_clean['actors']) > 255 )
            {
                $errors['actors'] = "Le nom du ou des acteurs doit contenir au maximum 255 carcatères.";
            }
        }

        // 3- Review input
        if ( isset($post_clean['review']) ) 
        {
            if (empty($post_clean['review']) && ($post_clean['review'] != 0) ) 
            {
                $errors['review'] = "La note du film est obligatoire.";
            }
            else if( ! is_numeric($post_clean['review']) )
            {
                $errors['review'] = "Veuillez entrer un nombre.";
            }
            else if( ($post_clean['review'] < 0) || ($post_clean['review'] > 5) )
            {
                $errors['review'] = "Veuillez une note comprise entre 0 et 5";
            }
        }

        
        if ( count($errors) > 0 ) 
        {
            // var_dump($errors); die();
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = $post_clean;
            return header("Location: " . $_SERVER['HTTP_REFERER']);
        }

        // On arrondie la note à 1 chiffre après la virgule
        $review = round($post_clean['review'], 1);

        require __DIR__ . "/db/connection.php";

        $req = $db->prepare("UPDATE film SET name=:name, actors=:actors, review=:review, updated_at=now() WHERE id=:id ");

        $req->bindValue(":name",   $post_clean['name']);
        $req->bindValue(":actors", $post_clean['actors']);
        $req->bindValue(":review", $review);
        $req->bindValue(":id",     $film['id']);

        $req->execute();
        $req->closeCursor();

        return header("Location: index.php");
    }



    

?>

<!-- -------------------------------View-------------------------------  -->

<?php $title = "Modification de film"; ?>

<?php require __DIR__ . "/partials/head.php"; ?>

    <?php require __DIR__ . "/partials/nav.php"; ?>

    <!-- Start the specific content for this page -->
    <main class="container-fluid">

        <h1 class="text-center my-3">Modification de : <em><?= $film['name'] ?><em></h1>

        <div class="container">

            <?php if( isset($_SESSION['errors']) && !empty($_SESSION['errors']) ) : ?>
                <div>
                    <ol>
                        <?php foreach( $_SESSION['errors'] as $error ) : ?>
                            <li><?= $error ?></li>
                        <?php endforeach ?>
                    </ol>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="name">Le nom du film</label>
                    <input type="text" name="name" class="form-control" id="name" value="<?php echo $_SESSION['old']['name'] ?? $film['name']; unset($_SESSION['old']['name']); ?>" >
                </div>
                <div class="mb-3">
                    <label for="actors">Le(s) acteur(s)</label>
                    <input type="text" name="actors" class="form-control" id="actors" value="<?php echo $_SESSION['old']['actors'] ?? $film['actors']; unset($_SESSION['old']['actors']); ?>">
                </div>
                <div class="mb-3">
                    <label for="review">La note / 5</label>
                    <input type="text" name="review" class="form-control" id="review" value="<?php echo $_SESSION['old']['review'] ?? $film['review']; unset($_SESSION['old']['review']); ?>">
                </div>
                <div class="mb-3">
                    <input type="submit" class="btn btn-primary" />
                </div>
            </form>
        </div>

    </main>
    <!-- End the specific content for this page -->

<?php require __DIR__ . "/partials/foot.php"; ?>