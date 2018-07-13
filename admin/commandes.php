<?php

require_once __DIR__ . '/../include/init.php';
adminSecurity();

require __DIR__ . '/../layout/top.php';

/**
 * Lister les commandes dans un tableau HTML :
 *  Id de la commande
 *  nom prénom de l'utilisateur qui a passé la commande
 *  montant formaté 
 *  date de la commande
 *  statut de la commande
 * date du statut
 * 
 * Passer le statut en liste déroulante de choix avec un bouton modifier pour changer le statut de la commande en BDD (nécessite un champs caché)
 */

$statut = [
    'en cours',
    'envoyée',
    'livrée',
    'annulée'
];

if(isset($_POST['modifierStatut'])){
    
   
    $newStatut = $_POST['newStatut'];
    
    
    $query = 'UPDATE commande SET statut = :newstatut, date_statut = now() WHERE id = :idcommande';
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':newstatut'     => $newStatut,
        ':idcommande'    => $_POST['id']
    ]);

}

$query = <<<SQL
SELECT c.*, u.nom, u.prenom
FROM commande c 
JOIN utilisateur u ON c.utilisateur_id = u.id  
SQL;


$stmt = $pdo->query($query);

$commandes = $stmt->fetchAll();

?>

<h1 class="mt-2">Gestion des commandes</h1>

<table class="table">
    
    <tr class="table text-center">
        <th>Id commande</th>
        <th>Utilisateur</th>
        <th>Montant commande</th>
        <th>Date de la commande</th>
        <th>Statut</th>
        <th>Modifier le statut</th>
        <th>Date du statut</th>
    </tr>
    
    <?php
    foreach ($commandes as $id => $commande) :
    ?>
    
    <tr class="text-center">
        <td><?= $commande['id']; ?></td>
        <td><?= $commande['prenom'] . ' ' . $commande['nom']; ?></td>
        <td><?= prixFr($commande['montant_total']);?></td>
        <td><?= dateTimeFr($commande['date_commande']); ?></td>
        <td><?= $commande['statut']; ?></td>
        <td>    
            <form method="post" style="display:inline">
                <select name="newStatut" value="<?=$commande['statut'];?>">
                  <?php
                  
                  foreach ($statut as $statut):
                      $selected = ($statut == $commande['statut'])
                          ? 'selected'
                          : ''
                          ;
                      ?>
                    <option value="<?=$statut?>" <?=$selected?>><?=$statut?></option>
                
                    <?php
                    endforeach;
                    ?>
                    
                </select>
                <input type="hidden" value="<?=$commande['id']?>" name="id">
                <button type="submit" name="modifierStatut">Ok</button>
            </form>
        </td>
        <td><?= dateTimeFr($commande['date_statut']) ?></td>
    </tr>
        
    <?php   
    endforeach;
    
    ?>
    
    
    
</table>



<?php

require __DIR__ . '/../layout/bottom.php';

?>


