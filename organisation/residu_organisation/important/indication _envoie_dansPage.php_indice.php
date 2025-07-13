<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../inc/fonction.php');
session_start();
if (!isset($_GET['indice'])) {
    die("Erreur : aucune propriété spécifiée.");
}
//echo "ID reçu : ".$_GET['indice'];
$id_propriete=$_GET['indice'];
//var_dump($id_propriete);

$touts=montretout();
$agents=montreAgent();
$infos=montreparPropriete($id_propriete);
var_dump($infos);

$_SESSION['id_client'];

$alls=prendreAgentParPropriete($_GET['indice']);
$agent=$alls[0];


//$id_propriete = (int) $_GET['indice'];


if ($_SERVER['REQUEST_METHOD']=='POST') {
    if (isset($_POST['date'],$_POST['heure'],$_POST['enfant'],$_POST['adulte'])) {
        $date=$_POST['date'];
        $heure=$_POST['heure'];
        $enfant=$_POST['enfant'];
        $adulte=$_POST['adulte'];
        $id_agent=$_POST['id_agent'];
        //echo $id_agent;
        enregistre_parametre($id_agent,$_SESSION['id_client'],$date,$heure,$enfant,$adulte);
        //exit();
        $message="commande enregistré avec succès";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <title>Accueil</title>
    <title>Document</title>
</head>
<body class="bg-light opacity-65">


<?php if(isset($message)){?>
    <div class="alert alert-success">
        <?= $message ?>
    </div>
<?php }?>

<main>
    <div class="container">
        <div class="row">
            <!--place carousel-->
            <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded g-col-6" style="width: 75%">
                <div class="row">
                    <?php foreach($infos as $info){?>
                    <div class="col text-start">
                        <p class="fs-4 fts-italic">ville: <?=$info['ville']?>. Type:<?=$info['type_maison']?></p>
                        <p class="fs-5 opacity-75"><?=$info['adresse']?></p>
                    </div>

                    <div class="col text-end">
                        <p class="btn btn-danger">$ <?=$info['prix']?></p>
                    </div>
                    <?php }?>
                </div>
                


            <div id="carouselExampleDark" class="carousel carousel-dark slide"><!-- ceci est le corp du carousel-->

                <div class="carousel-indicators"><!--indicateur pour savoir dans quel photo du slide tu te trouve dans notre cas il y en a 3 de photo-->
                    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>


                <div class="carousel-inner"><!--cadre de tout les photos (en gros c'est le cadre que gardera les photos)-->
                    <!--place pour les differentes photos-->
                    <div class="carousel-item active" data-bs-interval="10000">
                        <img src="image/int1_3.jpeg" class="d-block w-100" alt="1"><!--ici ce sont les image comme d'habitude-->
                            <div class="carousel-caption d-none d-md-block"><!--division des commentaires pour chaque photos-->
                                <h5>First slide label</h5>
                                <p>Some representative placeholder content for the first slide.</p>
                            </div>
                    </div>
                            
                                    <!--place pour les differentes photos-->
                    <div class="carousel-item" data-bs-interval="2000">
                        <img src="image/int2_3.jpeg" class="d-block w-100" alt="2"><!--ici ce sont les image comme d'habitude-->
                            <div class="carousel-caption d-none d-md-block"><!--division des commentaires pour chaque photos-->
                                <h5>Second slide label</h5>
                                <p>Some representative placeholder content for the second slide.</p>
                            </div>
                    </div>
                            
                                    <!--place pour les differentes photos-->
                    <div class="carousel-item">
                        <img src="image/int4_2.jpeg" class="d-block w-100" alt="3"><!--ici ce sont les image comme d'habitude-->
                            <div class="carousel-caption d-none d-md-block"><!--division des commentaires pour chaque photos-->
                                <h5>Third slide label</h5>
                                <p>Some representative placeholder content for the third slide.</p>
                            </div>
                    </div>
                </div>

                                <!--bouton de navigation-->
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
        </div>


    </div>

    <section class="col grid text-center g-col-4" style="--bs-rows: 3; --bs-columns: 3;">

                    <!--block rendez-vous-->                
        <div class="card p-3 shadow-sm" style="max-width: 400px; margin: auto;">
            <h5 class="card-title"><i class="bi bi-calendar-event"></i> Schedule a Tour</h5>
            <hr>

                <!--Important : on garde ?indice=... dans l'URL pour que $_GET['indice'] reste disponible
                Sinon, après un POST, le paramètre 'indice' serait perdu si on met juste action="page.php"-->
            <form action="page.php?indice=<?=$id_propriete?>" method="post">
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                        
                <div class="mb-3">
                    <label for="heure" class="form-label">Heure</label>
                    <input type="time" name="heure" class="form-control" required>
                    <input type="hidden" name="id_agent" value="<?=$agent['id_agent']?>">
                </div>

                <div class="row text-center align-items-center mb-3">
                    <div class="col">
                        <label>Adult</label><br>
                        <button type="button" class="btn btn-outline-secondary" onclick="modifier('adulte', -1)">−</button>
                        <input type="number" name="adulte" id="adulte" value="0" class="mx-1 text-center" style="width: 40px;" readonly>
                        <button type="button" class="btn btn-outline-secondary" onclick="modifier('adulte', 1)">+</button>
                    </div>
                    <div class="col">
                        <label>Children</label><br>
                        <button type="button" class="btn btn-outline-secondary" onclick="modifier('enfant', -1)">−</button>
                        <input type="number" name="enfant" id="enfant" value="0" class="mx-1 text-center" style="width: 40px;" readonly>
                        <button type="button" class="btn btn-outline-secondary" onclick="modifier('enfant', 1)">+</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-danger w-100">Submit Request</button>
            </form>
        </div>
                        
        <script>
        function modifier(champ, valeur) {
            let input = document.getElementById(champ);
            let val = parseInt(input.value) + valeur;
            if (val < 0) val = 0;
            input.value = val;
        }
        </script>

                                
                <!--block information agents-->
        <?php foreach($alls as $all){?>
            <div class="card p-3 shadow-sm" style="max-width: 400px; margin: auto;">
                <div class="card-body">
                    <h5 class="card-title">Information de l'agent</h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary">Agent:<?=$all['nom'].' '.$all['prenom']?></h6>
                    <p class="card-text">Région:<?=$all['region']?></p>
                    <p class="card-text">adresse proposé:<?=$all['adresse']?></p>
                </div>
            </div>
        <?php }?>

    </section>

    </div>
</main>





    



</body>
</html>