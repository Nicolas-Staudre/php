<?php
require_once __DIR__ . '/include/init.php';

unset($_SESSION['utilisateur']);
$_SESSION['panier'] = [];

header('Location: index.php');
die;

