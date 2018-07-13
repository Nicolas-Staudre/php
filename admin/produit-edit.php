<?php

require_once __DIR__ . '/../include/init.php';
adminSecurity();

$errors = [];
$nom = '';
$description = '';
$reference='';
$prix='';
$categorie='';
$photoActuelle = '';


/*
 * Faire le formulaire d'édition de produits
 *  - nom : champ text obligatoire
 *  - description : textarea obligatoire
 *  - référence : champ text obligatoire + test de sa longueur à 50 caractère max + vérification de l'unicité
 *  - prix : champs text - obligatoire
 *  - catégorie : select liste déroulante - obligatoire
 * Si le formulaire est correctement rempli : INSERT en bdd et redirection vers la page de liste des produits avec un message de confirmation
 * Sinon Affichage des messages d'erreurs + On garde les champs préremplis avec les valeurs avant correction
 * 
 * PUIS 
 * Adapter la page pour modification
 * Si on reçoit un id dans l'URL sans retour de POST, pré-remplissage du formulaire à partir de la bdd.
 * enregistrement en UPDATE au lieu d'un INSERT.
 * Adapter le contrôle de l'unicité de la référence pour exclure la référence du produit que l'on modifie de la requête.
 * 
 */




if (!empty($_POST)){
    
    
    
    sanitizePost();
    extract($_POST);
    
    
    if (empty($_POST['nom'])) {
        $errors[] = 'Le nom est obligatoire';
        }
    if (empty($_POST['description'])) {
        $errors[] = 'La description est obligatoire';
    }
    
    if (empty($_POST['reference'])){
        $errors[] = 'La référence est obligatoire';
    }else if (strlen($_POST['reference']) > 50){
        $errors[] = 'La référence ne doit pas dépasser 50 caractères';
    } else {
        $query = 'SELECT count(*) FROM produit WHERE reference = :reference';
        
        if (!empty($_GET['id'])){
            $query .= 'AND id !=' . (int)$_GET['id'];
        }
        
        $stmt = $pdo->prepare($query);
        
        $stmt->bindValue(':reference', $_POST['reference']);
        $stmt->execute();
        $nb = $stmt->fetchColumn();
        
        if ($nb != 0){
            $errors[] = 'La référence est déjà utilisée';
        }
        
    }
    if (empty($_POST['prix'])) {
        $errors[] = 'Le prix est obligatoire';
    }
    if (empty($_POST['categorie'])){
        $errors[] = 'Merci de choisir une catégorie';
    }
    
    if (!empty($_FILES['photo']['tmp_name'])) {
        if ($_FILES['photo']['size'] > 1000000) {
            $errors[] = 'La  photo ne doit pas dépasser 1Mo';
        }
        $allowedMimeTypes = [
            'image/jpeg',
            'image/gif',
            'image/png'
        ];
        if (!in_array($_FILES['photo']['type'], $allowedMimeTypes)){
            $errors[] = 'La photo doit être en jpg, gig ou png';
        }
    }
    
    if (empty($errors)){
        if (!empty($_FILES['photo']['tmp_name'])){
            $name = $_FILES['photo']['name'];
            $extension = substr($name, strrpos($name, '.'));
            
            $nom_photo = $_POST['reference'] . $extension;
            
            if (!empty($photoActuelle)){
                unlink(PHOTO_DIR . $photoActuelle);
            }
            
            move_uploaded_file($_FILES['photo']['tmp_name'], PHOTO_DIR . $nom_photo);
            
        } else {
            $nomPhoto = $photoActuelle;
        }
        
        if (isset($_GET['id'])) {
            $query = 'UPDATE produit SET nom=:nom, description=:description, reference=:reference, prix=:prix, categorie=:categorie, photo=:photo WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':nom'              =>  $_POST['nom'],
                ':description'      =>  $_POST['description'],
                ':reference'        =>  $_POST['reference'],
                ':prix'             =>  $_POST['prix'],
                ':categorie'        =>  $_POST['categorie'],
                ':id'               =>  $_GET['id'],
                ':photo'            =>  $_POST['photo'],
            ]);
        }else if (!empty($_GET['id'])){
            $query = 'INSERT INTO produitnom, description, reference, prix, categorie_id, photo VALUES :nom, :description, :reference, :prix, :categorie_id, :photo';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':nom'                  =>  $_POST['nom'],
                ':description'          =>  $_POST['description'],
                ':reference'            =>  $_POST['reference'],
                ':prix'                 =>  $_POST['prix'],
                ':categorie_id'         =>  $_POST['categorie'],
                ':photo'                =>  $_POST['photo'],
            ]);
        }
        setFlashMessage('Le produit est enregistré');
        header('Location: produits.php');
        die;
        
    }
    
} else {
    $query = 'SELECT * FROM produit WHERE id = ' . (int)$_GET['id'];
    $stmt = $pdo->query($query);
    
    $produit = $stmt->fetch();
    extract($produit);
    $categorie = $produit['categorie_id'];
    
}

// Je fais ma requête pour la liste déroulante.
$query = 'SELECT * FROM categorie';
$stmt = $pdo->query($query);

$categories = $stmt->fetchAll();

require __DIR__ . '/../layout/top.php';


if (!empty($errors)) : 
?>
<div class="alert alert-danger mt-2">
    <h5 class="alert-heading">Le formulaire contient des erreurs</h5>
    <?= implode('<br>', $errors); ?>
</div>
<?php
endif;
?>
<h1 class="mt-2">Edition produit</h1>


<form method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label>Nom</label>
        <input type="text" name="nom" class="form-control" value="<?= $nom; ?>">
    </div>
    
    <div class="form-group">
        <label>Description</label>
        <textarea type="text" name="description" class="form-control" cols="5" rows="10" value="<?= $description; ?>"><?= $description; ?></textarea>
    </div> 
    
    <div class="form-group">
        <label>Reference</label>
        <input type="text" name="reference" class="form-control" value="<?= $reference; ?>">
    </div> 
    
    <div class="form-group">
        <label>Prix</label>
        <input type="text" name="prix" class="form-control" value="<?= $prix ?>">
    </div> 
    
    <div class="form-group">
        <label>Catégorie</label>
        <select name='categorie' class="form-control">
            <option value="" selected="true"></option>
            <?php  // Ajout de la liste déroulante
            foreach ($categories as $cat) :
                $selected = ($cat['id'] == $categorie)
                    ? 'selected'
                    : ''
                    ;
            ?>
            <option value="<?=$cat["id"]?>"<?=$selected?>><?=$cat["nom"]?></option>
            <?php    
            endforeach;
            ?>
        </select>
    </div>     
    
    <div class="form-group">
        <label>Photo</label>
        <input type="file" name="photo" class="form-control">
    </div>
    <?php
    if (!empty($photoActuelle)) :
        echo '<p><img src="' . PHOTO_WEB . $photoActuelle . '" height="150px"></p>';
    endif;
    ?>
    <input type="hidden" name="photoActuelle" value="<?= $photoActuelle; ?>">
       
    <div class="form-btn-group text-right">
        <button type="submit" class="btn btn-primary">
            Enregistrer
        </button>
        <a class="btn btn-secondary" href="produits.php">
            Retour
        </a>
    </div>
</form>


<?php

require __DIR__ . '/../layout/bottom.php';

?>





