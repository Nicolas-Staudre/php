<?php
require_once __DIR__ . '/include/init.php';

$errors = [];
$civilite = $nom = $prenom = $email = $ville = $cp = $adresse = "";

if(!empty($_POST)){
    sanitizePost();
    extract($_POST);
    
    if (empty($civilite)){ // Ici civilité provient de extract($_POST).
        $errors[] = 'La civilité est obligatoire';
    }
    if (empty($nom)){
        $errors[] = 'Le nom est obligatoire';
    }
    if (empty($prenom)){
        $errors[] = 'Le prenom est obligatoire';
    }
    if (empty($email)){
        $errors[] = "l'email est obligatoire";
    // test de la validité de l'adresse email
    }else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "l'email n'est pas valide";
    }else {
        $query = 'SELECT count(*) AS nb FROM utilisateur WHERE email = :email';
        $stmt =  $pdo->prepare($query);
        $stmt->execute([
            ':email' => $email
        ]);
        $nb = $stmt->fetchColumn();
        if ($nb != 0){
            $errors[] = "L'email est déjà utilisé";
        }
    }
    if (empty($adresse)){
        $errors[] = "L'adresse est obligatoire";
    }
    if (empty($ville)){
        $errors[] = 'La ville est obligatoire';
    }
    if (empty($cp)){
        $errors[] = 'Le code postal est obligatoire';
        
    // La fonction strlen va compter le nombre de caractères dans la chaine. La fonction ctype_digit vérifie si la variable rentrée ne contient que des chiffres. 
    }else if (strlen($cp) != 5 || !ctype_digit($cp)){
        $errors[] = 'Le code postal est invalide';
    }
    if (empty($mdp)){
        $errors[] = 'Le mot de passe est obligatoire';
    } else if (!preg_match('/^[a-zA-Z0-9_-]{6,20}$/', $mdp)) { // Pour la regex le mot de passe doit contenir uniquement des minuscules, majuscules
        $errors[] = 'Le mot de passe doit faire entre 6 et 20 caractères et ne contenir que des chiffres, lettres, "_" et "-"';
    }
    if ($_POST['mdp'] != $_POST['mdp_confirm']) {
        $errors[] = "Le mot de passe et sa confirmation ne sont pas identiques";
    }
    
    if (empty($errors)) {
        $query = <<<EOS
INSERT INTO utilisateur (
                nom,       
                prenom, 
                email,
                mdp,
                civilite,
                ville,
                cp,
                adresse
) VALUES (
                :nom,
                :prenom,
                :email,
                :mdp,
                :civilite,
                :ville,
                :cp,
                :adresse
)
EOS;
        $stmt = $pdo->prepare($query);
        $stmt->execute([
                ':nom'          => $nom,
                ':prenom'       => $prenom,
                ':email'        => $email,
                ':mdp'          => password_hash($mdp, PASSWORD_BCRYPT), // Ici on crypte le mot de passe en base de donnée grâce à un algo natif de PHP
                ':civilite'     => $civilite,
                ':ville'        => $ville,
                ':cp'           => $cp,
                ':adresse'      => $adresse,      
        ]);
    }
    setFlashMessage('Votre compte est créé');
    
   header('Location: index.php');
   die;
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

<h1 class="mt-2">Inscription</h1>

<form method="post">
    
    <div class='form-group'>
        <label>Civilité</label>
        <select name='civilite' class="form-control">
            <option value=""></option>
            <option value="Mme"<?php if($civilite == 'Mme') {echo 'selected';} ?>>Mme</option>
            <option value="M."<?php if($civilite == 'M.') {echo 'selected';} ?>>M.</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>Nom</label>
        <input type='text' name="nom" class="form-control" value="<?= $nom; ?>">
    </div>
    
    <div class="form-group">
        <label>Prenom</label>
        <input type='text' name="prenom" class="form-control" value="<?= $prenom; ?>">
    </div>
    
    <div class="form-group">
        <label>email</label>
        <input type='text' name="email" class="form-control" value="<?= $email; ?>">
    </div>
    
    <div class="form-group">
        <label>Mot de passe</label>
        <input type='password' name="mdp" class="form-control">
    </div>
<div class="form-group">
        <label>Confirmation du mot de passe</label>
        <input type='password' name="mdp_confirm" class="form-control">
    </div>
    
    <div class="form-group">
        <label>Adresse</label>
        <textarea type='text' name="adresse" class="form-control"><?= $adresse; ?></textarea>
    </div>
    
    <div class="form-group">
        <label>Ville</label>
        <input type='text' name="ville" class="form-control" value="<?= $ville; ?>">
    </div>
    
    <div class="form-group">
        <label>Code Postal</label>
        <input type='text' name="cp" class="form-control" value="<?= $cp; ?>">
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
