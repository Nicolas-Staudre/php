<?php

// Fonction pour simplifier var_dump().
function dump($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

// Fonctions pour nettoyer les données récupérées
function sanitizeValue (&$value)
{
    // trim permet de retirer les espaces, tabulation et saut de lignes avant et après la chaine de caractère.
    // strip_tags supprime les balises HTML
    $value = trim(strip_tags($value));  
}

function sanitizeArray (array &$array) // Ici j'ai typé mon paramètre en array pour que ma fonction n'accepte que des paramètres de type array.
{
    // array walk parcours le tableau souhaité et applique la fonction situé en 2ème argument (ici sanitizeValue afin de nettoyer toutes les valeurs du tableau.
    array_walk($array, 'sanitizeValue');
}

function sanitizePost () // Ici on fait une fonction pour nettoyer rapidement les données du tableau $_POST.
{
    sanitizeArray($_POST);
}

// Enregistre un message dans le SESSION pour pouvoir l'utiliser partour car SESSION est une super globale.
function setFlashMessage($message, $type = 'success')
{
    $_SESSION['flashMessage'] = [
        'message'   => $message,
        'type'      => $type,
    ];
}

// Affiche le message qui est en SESSION SI il existe un message puis le supprime
function displayFlashMessage ()
{
    if (isset($_SESSION['flashMessage'])){
        $message = $_SESSION['flashMessage']['message'];
        // On fait un ternaire pour changer 'error' en 'danger' afin d'être comptatible avec Bootstrap.
        $type = ($_SESSION['flashMessage']['type'] == 'error')
                ? 'danger'
                : $_SESSION['flashMessage']['type'];
        
        echo    '<div class="alert alert-' . $type . ' mt-2">'
                . '<h5 class="alert-heading">' . $message . '</h5>'
                . '</div>';
        // On souhaite l'afficher qu'une seule fois donc on le supprime une fois afficher.
        unset($_SESSION['flashMessage']);
    }
}

function isUserConnected () 
{
    return isset($_SESSION['utilisateur']);
}

function getUserFullName ()
{
    if (isUserConnected()) {
        return $_SESSION['utilisateur']['prenom'] . " " . $_SESSION['utilisateur']['nom'];
    }
}

function isUserAdmin ()
{
    return isUserConnected() && ( $_SESSION['utilisateur']['role'] == 'admin' );
}

function adminSecurity ()
{
    if (!isUserAdmin()) {
        if (!isUserConnected()){
            header ('Location: ' . RACINE_WEB . 'connexion.php');
        } else {
            header('HTTP/1.1 403 Forbidden');
            echo '<img src=' . RACINE_WEB . '/images.jpg>'; 
            
        }
        
        die;
        
    }
}
function prixFr ($prix)
{
    return number_format($prix, 2, ',', ' ') . ' €';
}

function ajoutPanier (array $produit, $quantite)
{
    if (!isset($_SESSION['panier'])){
        $_SESSION['panier'] = [];
    }
    
    // Si le produit n'est pas encore dans le panier
    if (!isset ($_SESSION['panier'][$produit['id']])) {
        $_SESSION['panier'][$produit['id']] = [
            'nom'   => $produit['nom'],
            'prix'  => $produit['prix'],
            'quantite' => $quantite
        ];
    } else {
        // Si le produit est déjà dans le panier
        // On met à jour la quantité
        $_SESSION['panier'][$produit['id']]['quantite'] += $quantite;
    }
    
}

function calculPrixTotal ()
{
    $prixTotal = 0;

    if(isset($_SESSION['panier'])){
               
        foreach ($_SESSION['panier'] as $panier){
            $prixProduit = $panier['prix'] * $panier['quantite'];
            $prixTotal += $prixProduit;
        }
    }
    return $prixTotal;
}

function modifierQuantitePanier ($produitId, $quantite)
{
    if ($quantite == 0) {
        unset($_SESSION['panier'][$produitId]);
    }else {
        $_SESSION['panier'][$produitId]['quantite'] = $quantite;
    }

}

function dateTimeFr ($dateTimeSql)
{ 
    return date('d/m/Y H:i', strtotime($dateTimeSql));
}
