<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require("../inc/fonction.php");
session_start();
echo "OK index";
$tables=prendreAllDepartment();
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

    <br><br><br>
    <div class="mx-auto p-2" style="width: 500px;">
        <h1>Liste des departements</h1>
    </div>


    <table class="table table-light table-striped table-hover">
        <thead>
            <tr> 
                <th scope="col">Departement</th>
                <th scope="col">Manager</th>
                <th scope="col">Nombre Employé(e)s</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tables as $table) {?>
            <tr>
                <th scope="row"><a href="employer.php?indice=<?=$table['dept_no']?>" class="text-success" ><?=$table['dept_name']?></a></th>
                <td><?=$table['last_name'].' '.$table['first_name']?></td>
                <td><?= NombreEmployesParDepartement($table['dept_no']) ?></td>
            </tr>
            <?php }?>
        </tbody>
    </table>

</body>
</html>