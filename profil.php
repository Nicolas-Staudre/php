<?php

require_once __DIR__ . '/include/init.php';

require __DIR__ . '/layout/top.php';

$query = 'SELECT * FROM utilisateur WHERE id = ' . $_SESSION['utilisateur']['id'];

$stmt = $pdo->query($query);
$utilisateur = $stmt->fetch();

dump($utilisateur);


?>

<h1 class="mt-2 mb-5">Votre profil</h1>

<h3 class="mb-3">Vos infos</h3>

<p>Votre prénom : <span class="font-weight-bold"><?=$utilisateur['prenom']?></span></p>

<p>Votre nom : <span class="font-weight-bold"><?=$utilisateur['nom']?></span></p>




