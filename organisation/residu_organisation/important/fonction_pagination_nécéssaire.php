<?php
//place fonction pour pagination
function EmployerParDepartement($indice, $page, $parPage){
    $offset = ($page - 1) * $parPage;
    $requete="SELECT * from v_employees_deptemp_departements WHERE dept_no='%s' limit %d,%d";
    $requete=sprintf($requete,$indice,$offset, $parPage);
    $attente=mysqli_query(connection(),$requete);
    $tables=array();
    while($resultat=mysqli_fetch_assoc($attente)){
        $tables[]=$resultat;
    } 
    return $tables;
}

function NombreEmployesParDepartement($indice) {
    $requete = "SELECT COUNT(*) AS total FROM v_employees_deptemp_departements WHERE dept_no = '%s'";
    $requete = sprintf($requete, $indice);
    $attente = mysqli_query(connection(), $requete);
    $resultat = mysqli_fetch_assoc($attente);
    return (int)$resultat['total'];
}
//place fonction pour pagination

?>