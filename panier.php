<?php

require_once __DIR__ . '/include/init.php';

require __DIR__ . '/layout/top.php';


if (isset($_POST['modifierQuantite'])){
    
    modifierQuantitePanier($_POST['idProduit'], $_POST['newQuantite']);

}

if (isset($_POST['commander'])){
    $query = <<<SQL
INSERT INTO commande(
            utilisateur_id,
            montant_total
) VALUES (
            :utilisateur_id,
            :montant_total
)
SQL;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
            ':utilisateur_id'   => $_SESSION['utilisateur']['id'],
            ':montant_total'    => calculPrixTotal()
            ]);
    //Renvoie l'identifiant du dernier élément généré par la commande SQL.
    $commandeId = $pdo->lastInsertId();
    
    $query = <<<SQL
INSERT INTO detail_commande(
            commande_id,
            produit_id,
            prix,
            quantite
) VALUES (
            :commande_id,
            :produit_id,
            :prix,
            :quantite
)
SQL;
    
    $stmt = $pdo->prepare($query);
    
    foreach ($_SESSION['panier'] as $produitId => $produit){
        $stmt->execute([
           ':commande_id'       => $commandeId,
            ':produit_id'       => $produitId,
            ':prix'             => $produit['prix'],
            ':quantite'         => $produit['quantite']   
        ]);
    }
    
    $_SESSION['panier'] = [];
    
    setFlashMessage('La commande est bien enregistrée');
    
}


?>

<h1 class="mt-2">Panier</h1>

<?php

if (empty($_SESSION['panier'])) :

?>
<p>Votre panier est vide</p>

<?php
else :
?>

<table class="table">
    <tr class="table">
        <th>Nom du produit</th>
        <th>Prix unitaire</th>
        <th>Quantité</th>
        <th>Prix total du produit</th>
    </tr>
    
<?php
        foreach ($_SESSION['panier'] as $id => $panier) :
?>
    <tr class="table">
        <td><?= $panier['nom']?></td>
        <td><?= prixFr($panier['prix'])?></td>
        <td><?= $panier['quantite']?> 
            <form style="display:inline" method="post">
                <input type="number" name="newQuantite" min="0" style="width:40px" value="<?=$panier['quantite']?>">
                <input type="hidden" name="idProduit" value="<?=$id?>">
                <button type="submit" name="modifierQuantite">Ok</button>
            </form>
            
        
        </td>
        <td><?= prixFr(($panier['prix'] * $panier['quantite'])) ?></td>

    </tr>    
<?php
endforeach;


?>
    <tr class="table">
        <td colspan=3><strong>Prix Total</strong></td>
        <td><strong><?= prixFr(calculPrixTotal())?></strong></td>
    </tr>
    
</table>


<?php

    if (!isUserConnected()) :
    ?>
        <div class="alert alert-info">
            <p>Vous devez vous connecter ou vous inscire pour valider la commande</p>  
        </div>
    <?php
    else :
    ?>
        <form method="post">
            <p class="text-right">
                <button type="submit" name="commander" class="btn btn-primary">
                    Valider la commande
                </button>
            </p>
        </form>


        
<?php        
    endif;
endif;
?>







<?php
require __DIR__ . '/layout/bottom.php';
?>