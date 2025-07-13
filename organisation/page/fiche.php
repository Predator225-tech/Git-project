<?php
require('../inc/fonction.php');
session_start();
$indice=$_GET['indices'];
$tables=FicheEmployer($indice);
$titres=historique_emploie($indice);

$_SESSION['nom']=$tables[0]['last_name'];
$_SESSION['prenom']=$tables[0]['first_name'];//ces trois session vienne de FicheEmployer
$_SESSION['emp_no']=$tables[0]['emp_no'];
$_SESSION['departements']=$tables[0]['dept_name'];
$_SESSION['indice']=$tables[0]['dept_no'];
//var_dump($_SESSION['indice']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <title>fiche employer</title>
</head>
<body>

<?php require_once('entète/header.php');?>

    <br><br><br>
    <section class="card text-center fixed">
        <div class="card-header">
            <h3><?=$tables[0]['first_name'].' '.$tables[0]['last_name'] ?></h3>
            <h3> Gender : <?=$tables[0]['gender'] ?></h3>
        </div>
        
        <div class="card-body">
                <h5 class="card-title">Departements: <?=$tables[0]['dept_name'] ?></h5>
                <h5 class="card-title">Numéro Departement : <?=$tables[0]['dept_no'] ?></h5>



                <article class="card-text">
                <h4 class="text-center fixed"><a href="formulaire1.php?index=<?=$tables[0]['emp_no'] ?>" class="btn btn-warning">Changer de departement</a></h4>
                <h6 class="text-center fixed"><a href="index.php" class="btn btn-success">Devenir manager</a></h6>

<br><br>
            <article class="card-text">
                <h4></h4>
                <h2>Historique des salaires</h2>
                <table class="table table-light table-striped table-hover">
                    <thead>
                        <tr> 
                            <th scope="col bg-white">Salaire</th>
                            <th scope="col">from</th>
                            <th scope="col">to</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tables as $table) {?>
                        <tr>
                            <th scope="row"><?=$table['salaire']?> $ </th>
                            <td><?=$table['safrom_date']?></td>
                            <td><?=$table['sato_date']?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </article>

<h1>historique des emplois</h1>
<section class="container mt-5">
    <table class="table table-bordered table-hover mt-3">
        <thead class="table-light">
            <tr>
                <th>Identifiant</th>
                <th>Numéro Département</th>
                <th>Département</th>
                <th>début de fonction</th>
                <th>fin de fonction</th>
                <th>Titre</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($titres as $titre){?>
            <tr>
                <td><?= $titre['emp_no'] ?></td>
                <td><?= $titre['dept_no']?></td>
                <td><?= $titre['dept_name']?></td>
                <td><?= $titre['début'] ?></td>
                <td><?= $titre['fin'] ?></td>
                <td><?= $titre['title']?></td>
            </tr>
            <?php }?>
        </tbody>
    </table>
</section>

<br><br><br>
            <a href="index.php" class="btn btn-primary">revenir vers l'accueil</a>
            <center><a href="employer.php?indice=<?=$tables[0]['dept_no'] ?>" class="btn btn-danger">retour</a></center>
        <br><br><br>
        </div>
        
    </section>
</body>
</html>