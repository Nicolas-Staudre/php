<?php
require_once __DIR__ . '/include/init.php';

$email = "";
$errors = [];

if (!empty($_POST)) {
    sanitizePost();
    extract($_POST);
    
    if(empty($email)) {
        $errors[] = "L'email doit être renseigné";
    }
    if(empty($mdp)) {
        $errors[] = "Le mot de passe doit être renseigné";
    }
    
    if (empty($errors)){
       $query = 'SELECT * FROM utilisateur WHERE email = :email';
       $stmt = $pdo->prepare($query);
       $stmt->execute([':email' => $email]);
       $utilisateur = $stmt->fetch();
       
       // On vérifie si l'email est bien entré en BDD
       if (!empty($utilisateur)){
           // On vérifie le mot de passe saisi si celui ci correspond au mot de passe crypté en BDD
           if (password_verify($mdp, $utilisateur['mdp'])){// Le fonction password_verify permet de décrypté le mot de passe.
                                  // Connecter un utilisateur, c'est l'enregistrer en session
                   $_SESSION['utilisateur'] = $utilisateur;
                   $_SESSION['panier'] = [];
                    header('Location: index.php');
                    die;
           }
        $errors[] = "Identifiant ou mot de passe incorrect";
       }
    }
}

require __DIR__ . '/layout/top.php';

if (!empty($errors)) :
?>
    <div class="alert alert-danger mt-2">
        <h5 class="alert-heading">Le formulaire contient des erreurs</h5>
        <?= implode('<br>', $errors); // on utilise la fonction IMPLODE() pour transformer les tableaux en chaine de caractères. La fonction IMPLODE se décompose comme cela IMPLODE (séparateur, tableau)?>
    </div>
<?php
endif;
?>

    <h1>Connexion</h1>
    
    <form method="post">
        <div class="form-group">
            <label>email</label>
            <input type='text' name="email" class="form-control" value="<?= $email; ?>">
        </div>
        <div class="form-group">
            <label>Mot de passe</label>
            <input type='password' name="mdp" class="form-control">
        </div>        
        <div class="mb-2">
            <button type="submit" class="btn btn-primary">
                Valider
            </button>
        </div>       
    </form>
    
<?php
require __DIR__ . '/layout/bottom.php';
?>

    