<?php

require_once __DIR__ . '/../include/init.php';
adminSecurity();

require __DIR__ . '/../layout/top.php';

// Lister les produits dans un tableau html tous les champs sauf la desciption.
// Bonus : 
// afficher le nom de la catégorie  au lieu de son id

$query = <<<EOS
SELECT p.*, c.nom AS Nom_categorie
FROM produit p
JOIN categorie c ON c.id = p.categorie_id

EOS;

$stmt = $pdo->query($query);
$produits = $stmt->fetchAll();

?>

<h1 class="mt-2">Gestion des produits</h1>

<p><a href="produit-edit.php">Ajouter un produit</a></p>

<table class="table">
    
    <tr class="table">
        <th>Référence</th>
        <th>Catégorie</th>
        <th>Nom</th>
        <th>Prix</th>
        <th></th>
    </tr>
    
    <?php 
        foreach ($produits as $produit) :
    ?>
    
    <tr>
        <td> <?= $produit['reference'] ?></td>
        <td> <?= $produit['Nom_categorie'] ?></td>
        <td> <?= $produit['nom'] ?></td>
        <td> <?= prixFr ($produit['prix']) ?></td>
        <td> 
            <a class="btn btn-primary" href="produit-edit.php?id=<?= $produit['id']; ?>">Modifier</a> 
            <a class="btn btn-danger" href="produit-delete.php?id=<?= $produit['id']; ?>">Supprimer</a>
        </td>
    </tr>
    
    <?php
    endforeach;
    ?>
</table>



<?php
require __DIR__ . '/../layout/bottom.php';
?>