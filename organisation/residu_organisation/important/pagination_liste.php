<?php
require("../inc/fonction.php");
session_start();
$indice=$_GET['indice'] ?? null;//ici c'est une douille
$_SESSION['indice']=$indice;
//var_dump($indice);


$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$parPage = 20;
// Nombre total d'employés dans ce département
$total = NombreEmployesParDepartement($indice);
$pages = max(1, ceil($total / $parPage));
// Si la page demandée dépasse le max, on la limite
if ($page > $pages) {
    $page = $pages;
}
// Récupérer les employés pour la page courante
$touts = EmployerParDepartement($indice, $page, $parPage);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <title>Document</title>
</head>
<body>
    <?php require_once('entète/header.php');?>
<br>
<br>
<br>
 
<article class="mx-auto p-2" style="width: 500px;">
    <h1>Liste des employés: <span class="text-danger"><?= htmlspecialchars($touts[0]['dept_name']) ?></span></h1>
</article>

<section class="border border-dark">    
    <table class="table table-light table-striped table-hover">
        <thead class="table-light">
            <tr>
                <th scope="col">Identifiant</th>
                <th scope="col">Numéro département</th>
                <th scope="col">Département</th>
                <th scope="col">Employer</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($touts as $tout){?>
            <tr>
                <th scope="row"><?=$tout['emp_no']?></th>
                <td><?=$tout['dept_no']?></td>
                <td><?=$tout['dept_name']?></td>
                <td><a href="fiche.php?indices=<?=$tout['emp_no']?>"><?=$tout['last_name'].' '.$tout['first_name']?></a></td>
            </tr>
            <?php }?>
        </tbody>
    </table>
</section>


<!-- Pagination centrée -->
    <?php if ($pages > 1): ?>
    <div class="mt-3">
        <nav>
            <ul class="pagination justify-content-center">
                <!-- Lien Précédent -->
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?indice=<?= $indice ?>&page=<?= $page - 1 ?>">← Précédent</a>
                    </li>
                <?php endif; ?>

                <?php
                $maxLinks = 5;
                $start = max(1, $page - floor($maxLinks / 2));
                $end = min($pages, $start + $maxLinks - 1);

                // Ajustement si on est à la fin
                if (($end - $start + 1) < $maxLinks && $start > 1) {
                    $start = max(1, $end - $maxLinks + 1);
                }
                ?>

                <!-- Liens de pages -->
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?indice=<?= $indice ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Lien Suivant -->
                <?php if ($page < $pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?indice=<?= $indice ?>&page=<?= $page + 1 ?>">Suivant →</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>


</body>
</html>