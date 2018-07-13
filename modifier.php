<?php

require_once __DIR__ . '/include/init.php';

require __DIR__ . '/layout/top.php';

$query = 'SELECT * FROM utilisateur WHERE id = ' . $_SESSION['utilisateur']['id'];

$stmt = $pdo->query($query);

$utilisateur = $stmt->fetch();

?>

<h1 class="mt-2">Modifier votre profil</h1>


    