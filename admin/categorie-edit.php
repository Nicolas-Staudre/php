<?php

require_once __DIR__ . '/../include/init.php';
adminSecurity();

$errors = [];
$nom = '';


if (!empty($_POST)){ // Si le formulaire a été soumis
    sanitizePost(); // On appelle la fonction pour nettoyer le tableau $_POST (fonction appellée grâce au fichier init qui lui même appelle le fichier fonctions).
    extract($_POST); // La fonction extract() permet de créer directement des variables à partir d'un tableau. Les variables créées ont comme nom les clés du tableau et comme valeur les valeur de chaque clé.
    // test de la saisie du champs nom
    if (empty($_POST['nom'])) {
        $errors[] = 'Le nom est obligatoire';
    }else if(strlen($_POST['nom']) > 50) {
        $errors[] = 'Le nom ne doit pas dépasser 50 caractères.';
    }
    
    // Si $errors est vide alors on peut enregistrer en BDD
    if (empty($errors)){
       
        if (isset($_GET['id'])) { // Modification
            $query = 'UPDATE categorie SET nom = :nom WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':nom'  => $nom,
                ':id'   => $_GET['id']
            ]);
        }else { // insertion  en BDD
            $query = 'INSERT INTO categorie(nom) VALUES (:nom)';
            $stmt = $pdo->prepare($query);
            $stmt->execute([':nom' => $nom]);
        }
        
        // Enregistrement du message en $_SESSION.
        setFlashMessage('La catégorie est enregistrée');
        // redirection vers la page de liste
        header('location: categories.php');
        die; // termine l'execution du script pour ne pas executer ce qu'il y a en dessous.
    }
} else if (isset($_GET['id'])) {
    // En modification, si on n'a pas de retour de formulaire, on va chercher la catégorie en bdd pour afficher la catégorie souhaitée.
    $query = 'SELECT * FROM categorie WHERE id=' . (int)$_GET['id'];
    $stmt = $pdo->query($query);
    
    $categorie = $stmt->fetch();
    $nom = $categorie['nom'];
}

require __DIR__ . '/../layout/top.php';

if (!empty($errors)) :
?>
    <div class="alert alert-danger mt-2">
        <h5 class="alert-heading">Le formulaire contient des erreurs</h5>
        <?= implode('<br>', $errors); // on utilise la fonction IMPLODE() pour transformer les tableaux en chaine de caractères. La fonction IMPLODE se décompose comme cela IMPLODE (séparateur, tableau)?>
    </div>
<?php
endif;
?>

<h1 class="mt-2">Edition catégorie</h1>

<form method="post">
    <div class="form-group">
        <label>Nom</label>
        <input type="text" name="nom" class="form-control" value="<?= $nom; ?>">
    </div>
    <div class="form-btn-group text-right">
        <button type="submit" class="btn btn-primary">
            Enregistrer
        </button>
        <a class="btn btn-secondary" href="categories.php">
            Retour
        </a>
    </div>
</form>

<?php

require __DIR__ . '/../layout/bottom.php';

?>

