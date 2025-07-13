<?php
require('../inc/fonction.php');
session_start();

$age = $_GET['age'] ;
$age1 = $_GET['age1'];
$nom = $_GET['nom'];
$departement = $_GET['departement'];
$male=$_GET['M'];
$femelle=$_GET['F'];
$parPage = ($_GET['affiche'] ?? 20);
$page = max(1,($_GET['page'] ?? 1));
$offset = ($page - 1) * $parPage;

$resultats = cherche($age, $age1, $nom, $departement, $parPage, $offset,$male,$femelle);
$total = nombreTotalCherche($age, $age1, $nom, $departement,$male,$femelle);
$pages = max(1, ceil($total / $parPage));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat de la recherche</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php require_once('entète/header.php')?>


<br><br><br>

<section class="container mt-5">
    <h2>Résultats (<?= $total ?> employés trouvés)</h2>
    <table class="table table-bordered table-hover mt-3">
        <thead class="table-light">
            <tr>
                <th>Identifiant</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Age</th>
                <th>Département</th>
                <th>Genre</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultats as $emp){?>
            <tr>
                <td><?= $emp['emp_no'] ?></td>
                <td><a href="fiche.php?indices=<?=$emp['emp_no']?>"><?= htmlspecialchars($emp['last_name']) ?></a></td>
                <td><?= htmlspecialchars($emp['first_name']) ?></td>
                <td><?= $emp['age'] ?></td>
                <td><?= $emp['dept_name'] ?></td>
                <td><?= $emp['gender']?></td>
            </tr>
            <?php }?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($page > 1){?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">← Précédent</a>
                </li>
            <?php }?>

            <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++){?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                </li>
            <?php }?>

            <?php if ($page < $pages){?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Suivant →</a>
                </li>
            <?php }?>
        </ul>
    </nav>
</section>

<div class="mt-3">
    <a href="recherche.php">retour</a>
</div>



</body>
</html>
