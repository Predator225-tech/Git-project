<?php
require('../inc/fonction.php');
session_start();
$deps = prendreDepartementSimple(); // récupère tous les départements
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche employés</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php require_once('entète/header.php')?>

<br><br><br>

<div class="container mt-5">
    <h1 class="text-center text-info">Recherche d'employés</h1>
    <form action="traitement.php" method="get" class="mt-4">

        <div class="row mb-3">
            <div class="col">
                <label for="nom">Nom ou prénom</label>
                <input type="text" name="nom" class="form-control" placeholder="ex: Huan">
            </div>
            <div class="col">
                <label for="M">Homme seulement </label>
                <input type="checkbox" name="M" id="M" value="M">
            </div>
            <div class="col">
                <label for="F">Femme seulement </label>
                <input type="checkbox" name="F" id="F" value="F">
            </div>
            <div class="col">
                <label for="departement">Département</label>
                <select name="departement" class="form-select">
                    <option value="0">Tous</option>
                    <?php foreach($deps as $dep){?>
                        <option value="<?= $dep['dept_no'] ?>"><?= $dep['dept_name'] ?></option>
                    <?php }?>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label>Âge minimum</label>
                <input type="number" name="age" class="form-control" value="0" min="0">
            </div>
            <div class="col">
                <label>Âge maximum</label>
                <input type="number" name="age1" class="form-control" value="0" min="0">
            </div>
            <div class="col">
                <label>Résultats par page</label>
                <select name="affiche" class="form-select">
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </div>

    </form>
</div>
</body>
</html>
