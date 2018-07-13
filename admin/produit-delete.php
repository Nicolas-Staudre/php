<?php

require_once __DIR__ . '/../include/init.php';
adminSecurity();

$query = 'SELECT photo FROM produit WHERE id = ' . (int)$_GET['id'];
$stmt = $pdo->query($query);
$photo = $stmt->fetchColumn();

if (!empty($photo)) {
    unlink(PHOTO_DIR . $photo);
}

$query = 'DELETE FROM produit WHERE id= ' . (int)$_GET['id'];
$pdo->exec($query);

setFlashMessage('Le produit est supprimé');
header('location: produits.php');
die;