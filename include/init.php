<?php
// On démarre notre $_SESSION.
session_start();

define('RACINE_WEB', '/Travail_Nico/back/php/boutique/');
define('PHOTO_DIR', $_SERVER['DOCUMENT_ROOT'] . '/Travail_Nico/back/php/boutique/');
define('PHOTO_WEB', RACINE_WEB . 'photo/');
define('PHOTO_DEFAULT', "https://dummyimage.com/600x400/b3b3b1/ffffff&text=Pas+d'image");

require_once __DIR__ . '/cnx.php';
require_once __DIR__ . '/fonctions.php';

