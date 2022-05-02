<?php
session_start(); // Autorisation pour utiliser les sessions

    // Si la méthode d'envoi des données est "POST"
    if ( $_SERVER['REQUEST_METHOD'] === "POST" ) 
    {

        // var_dump($_POST); die();

        $post_clean = [];
        $errors     = [];

        // Protégeons-nous contre les failles de type XSS
        foreach ($_POST as $key => $value) 
        {
            $post_clean[$key] = strip_tags(trim($value));
        }

        
        // Procédons à la validation des données de chaque input

        // 1- Input name
        if ( isset($post_clean['name']) ) 
        {
            if ( empty($post_clean['name']) ) 
            {
                $errors['name'] = "Le nom du film est obligatoire.";
            }
            else if( mb_strlen($post_clean['name']) > 255 )
            {
                $errors['name'] = "Le nom du film doit contenir au maximum 255 carctères.";
            }
        }

        // 2- Input actors
        if ( isset($post_clean['actors']) ) 
        {
            if ( empty($post_clean['actors']) )
            {
                $errors['actors'] = "Le nom du ou des acteurs du film est obligatoire.";
            }
            else if( mb_strlen($post_clean['actors']) > 255 )
            {
                $errors['actors'] = "Le nom du ou des acteurs doit contenir au maximum 255 carctères.";
            }
        }

        // 3- Input review
        if ( isset($post_clean['review']) ) 
        {
            if ( empty($post_clean['review']) && ($post_clean['review'] != '0') )
            {
                $errors['review'] = "La note du film est obligatoire.";
            }
            else if( ! is_numeric($post_clean['review']) )
            {
                $errors['review'] = "La note doit être un nombre.";
            }
            else if( ($post_clean['review'] < 0) || ($post_clean['review'] > 5) )
            {
                $errors['review'] = "Veuillez entrer une note comprise entre 0 et 5.";
            }
        }


        /*
         * Si il y a des erreurs, 
         * on redirige l'utilisateur vers la page de laquelle proviennent les informations avec 
         * et on arrete l'exécution du script 
         * les messages d'erreurs qui vont avec
        */
        if ( count($errors) > 0 ) 
        {
            $_SESSION['old']    = $post_clean;
            $_SESSION['errors'] = $errors;
            return header("Location: " . $_SERVER['HTTP_REFERER']);
        }

        $review = round($post_clean['review'], 1);


        /*
         * Dans le cas où il n'y a pas d'erreurs, on insère les données dans l table 'film' de la base de données  
         */

        // connection à la base de données
        require __DIR__ . "/db/connection.php";

        // Requête d'insertion des données dans la tabel 'film'
        $req = $db->prepare("INSERT INTO film (name, actors, review, created_at, updated_at) VALUES (:name, :actors, :review, now(), now() ) ");

        // On envoie les vraies données
        $req->bindValue(":name",   $post_clean['name']);
        $req->bindValue(":actors", $post_clean['actors']);
        $req->bindValue(":review", $review);

        // On exécute la requête
        $req->execute();

        // On ferme la connexion avec la base de données (optionnel)
        $req->closeCursor();

        // Redirection vers la page d'accueil
        return header("Location: index.php");

    }
?>

<!-- ------------------------------------View------------------------------- -->

<?php $title = "Nouveau film"; ?>

<?php require __DIR__ . "/partials/head.php"; ?>

    <?php require __DIR__ . "/partials/nav.php"; ?>

    <!-- Start the specific content for this page -->
    <main class="container-fluid">

        <h1 class="text-center my-3">Nouveau film</h1>

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
                    <input type="text" name="name" class="form-control" id="name" value="<?php echo $_SESSION['old']['name'] ?? ''; unset($_SESSION['old']['name']); ?>" >
                </div>
                <div class="mb-3">
                    <label for="actors">Le(s) acteur(s)</label>
                    <input type="text" name="actors" class="form-control" id="actors" value="<?php echo $_SESSION['old']['actors'] ?? ''; unset($_SESSION['old']['actors']); ?>">
                </div>
                <div class="mb-3">
                    <label for="review">La note / 5</label>
                    <input type="text" name="review" class="form-control" id="review" value="<?php echo $_SESSION['old']['review'] ?? ''; unset($_SESSION['old']['review']); ?>">
                </div>
                <div class="mb-3">
                    <input type="submit" class="btn btn-primary" />
                </div>
            </form>
        </div>

    </main>
    <!-- End the specific content for this page -->

<?php require __DIR__ . "/partials/foot.php"; ?>