<?php
require_once(__DIR__ . '/../../inc/fonction.php');
$Alls = prendreAllDepartment(); // récupère la liste des départements
?>
<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
            
        <!--disposition des compositions du canvas-->
    
        <a class="navbar-brand" href="index.php"><img src="../inc/test_db_master/images/home.png" widht="50px" height="50px"></a>
        <a class="navbar-brand" href="recherche.php"><img src="../inc/test_db_master/images/recherche1.png" widht="50px" height="50px">Recherche</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            

        <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">          
            <!--bouton d'accès du offcanvas-->
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">Outils supplémentaire</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
                <!--corp du offcanvas-->
            <div class="offcanvas-body rounded-start">                
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Département
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <?php foreach($Alls as $All){?>
                            <li><a class="dropdown-item" href="employer.php?indice=<?=$All['dept_no']?>"><?=$All['dept_name']?></a></li>
                            <?php }?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="recherche.php">Rechercher les employé(e)s</a>
                    </li>
                </ul>        
            </div>
        </div>

        
    </div>
</nav>


