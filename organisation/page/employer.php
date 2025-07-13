<?php
require("../inc/fonction.php");
session_start();
$indice = $_GET['indice'];
$_SESSION['indice'] = $indice;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$parPage = 15;
$total = NombreEmployesParDepartement($indice);
$pages = max(1, ceil($total / $parPage));
if ($page > $pages) {
    $page = $pages;
}
$touts = EmployerParDepartement($indice, $page, $parPage);
$moyenne = salaire_moyen($indice);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Liste des Employés</title>
</head>
<body>
    <?php require_once('entète/header.php'); ?>

    <br><br><br>

    <article class="mx-auto p-2" style="width: 500px;">
        <h1>Liste des employés: <span class="text-danger"><?= htmlspecialchars($touts[0]['dept_name']) ?></span></h1>
        <h4>Salaire moyen pour le département: <?= $moyenne ?></h4>
        <h4>Numéro de département : <?=$touts['0']['dept_no']?></h4>
    </article>
    
    <section class="border border-dark mx-3">
        <table class="table table-light table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th scope="col">Département</th>
                    <th scope="col">Employé</th>
                    <th scope="col">Identifiant</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($touts as $tout): ?>
                    <tr>
                        <td><?= $tout['dept_name'] ?></td>
                        <td><a href="fiche.php?indices=<?= $tout['emp_no'] ?>"><?= $tout['last_name'] . ' ' . $tout['first_name'] ?></a></td>
                        <th scope="row"><?= $tout['emp_no'] ?></th>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- Simplified Pagination -->
    <?php if ($pages > 1): ?>
        <nav aria-label="Pagination des employés" class="mt-4">
            <ul class="pagination justify-content-center">
                <!-- Previous Page -->
                <li class="page-item <?= ($page <= 1) ?>">
                    <a class="page-link" href="?indice=<?= $indice ?>&page=<?= $page - 1 ?>" aria-label="Page précédente">← Précédent</a>
                </li>

                <!-- Next Page -->
                <li class="page-item <?= ($page >= $pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?indice=<?= $indice ?>&page=<?= $page + 1 ?>" aria-label="Page suivante">Suivant →</a>
                </li>
            </ul>
        </nav>

        <!-- Additional Navigation Buttons -->
        <div class="d-flex justify-content-center gap-3 mt-3">
            <a href="info1.php?indiceses=<?= $touts[0]['dept_no'] ?>" class="btn btn-primary">Voir liste des hommes</a>
            <a href="info2.php?indiceses=<?= $touts[0]['dept_no'] ?>" class="btn btn-primary">Voir liste des femmes</a>
        </div>
    <?php endif; ?>
</body>
</html>