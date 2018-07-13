<?php

require_once __DIR__ . '/include/init.php';

require __DIR__ . '/layout/top.php';

$query = 'SELECT prenom, nom, email, adresse, ville, role FROM utilisateur WHERE id = ' . $_SESSION['utilisateur']['id'];

$stmt = $pdo->query($query);
$utilisateur = $stmt->fetch();




?>

<h1 class="mt-2 mb-5">Votre profil</h1>

<h3 class="mb-3">Vos infos</h3>

<div class="container row">
    <div class="col">
    <?php

    foreach ($utilisateur as $id => $user) :
    ?>
    <p>Votre <?= $id ?> : <span class="font-weight-bold"><?=$user?></span></p>
    <?php
    endforeach;
    ?>

<button class="btn btn-primary"><a class="badge badge-primary" href="<?=RACINE_WEB?>modifier.php">Modifier</a></button>
    </div>
    <div class="col">
        
        <div class="image">
            <img src="">
        </div>
        
    </div>
</div>