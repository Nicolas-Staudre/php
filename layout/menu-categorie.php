<?php

$stmt = $pdo->query('SELECT * FROM categorie');
$categoriesMenu = $stmt->fetchAll();

?>

<div class="navbar-collapse">
    
    <ul class="navbar-nav">
        <?php
        foreach ($categoriesMenu as $categorieMenu) :
        ?>    
        <li class="nav-item">
            <a class="nav-link" href="<?= RACINE_WEB?>categorie.php?id=<?= $categorieMenu['id'];?>">
                <?= $categorieMenu['nom']; ?>
            </a>
        </li>
        
        <?php
        
        endforeach;
        
        
        ?>
    </ul>
    
</div>

