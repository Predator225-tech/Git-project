<?php
require('../inc/fonction.php');
session_start();
$index=$_GET['index'];
$_SESSION['index']=$index;
$deps = prendreDepartementSimple();
$tables=FicheEmployer($index);
//var_dump($tables[0]['emp_no']);
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

<br><br>
<?php if (isset($_GET['erreur']) && $_GET['erreur'] == 1) {?>
    <div class="alert alert-danger" role="alert">
        Veuillez remplir tous les champs correctement avant de soumettre le formulaire.
    </div>
<?php }?>

<section class="container mt-5">
    <section class="card text-center" style="width: 70rem;">
    <div class="card-header">
        <h1>Nom de l'utilisateur:<?=$tables[0]['first_name'].' '.$tables[0]['last_name']?></h1>  
        <h4>Departement: <span class="text-primary"><?=$tables[0]['dept_name']?></span></h4>

    </div>

        <form action="traitement1.php" method="post">
            <br>
            <div class="col">
                <label for="date">Date</label>
                <input type="date" name="date" id="date">
            </div>
            <br>
            <div class="col">
                <label for="departement">Département</label>
                <select name="departement" class="form-select">
                    <option value="0">Tous</option>
                    <?php foreach($deps as $dep){?>
                        <option value="<?= $dep['dept_no'] ?>"><?= $dep['dept_name'] ?></option>
                    <?php }?>
                </select>
            </div>

            <button type="submit" class="btn btn-danger">Confirmer</button>
        </form>
    </section>
</section>
<a href="fiche.php?indices=<?=$tables[0]['emp_no'] ?>" class="btn btn-danger">retour</a>
</body>
</html>