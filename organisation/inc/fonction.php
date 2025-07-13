<?php
require('connexion.php');

function prendreAllDepartment(){
    $requete="SELECT * from v_employees_deptmanage_departments where to_date ='9999-01-01'";
    $attente=mysqli_query(connection(),$requete);
    $tables=array();
    while($resultat=mysqli_fetch_assoc($attente)){
        $tables[]=$resultat;
    }
    return $tables;
}



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
function FicheEmployer($indices){
    $requete="SELECT * from v_information where emp_no='%d' group by sato_date desc";
    $requete=sprintf($requete,$indices);
    $attente=mysqli_query(connection(),$requete);
    $table=array();
    while($resultat=mysqli_fetch_assoc($attente)){
        $table[]=$resultat;
    }
    return $table;
}


/*pour la liste déroulante pour l'offcanvas droite */
function prendreDepartementSimple(){
    $requete="SELECT * from departments";
    $attentes=mysqli_query(connection(),$requete);
    $tables=array();
    while($resultat=mysqli_fetch_assoc($attentes)){
        $tables[]=$resultat;
    }
    return $tables;
}

function cherche($age, $age1, $nom, $departement, $limites, $offset,$male,$femelle) {
    $conditions = [];
    $params = [];

    if (!empty($departement) && $departement !== "0") {
        $conditions[] = "d.dept_no = '%s'";
        $params[] = $departement;
    }

    if ($age > 0) {
        $conditions[] = "TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) >= %d";
        $params[] = $age;
    }

    if ($age1 > 0) {
        $conditions[] = "TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) <= %d";
        $params[] = $age1;
    }

    if (!empty($nom)) {
        $conditions[] = "(LOWER(e.first_name) LIKE LOWER('%%%s%%') OR LOWER(e.last_name) LIKE LOWER('%%%s%%'))";
        $params[] = $nom;
        $params[] = $nom;
    }

    if (!empty($male) && !empty($femelle)) {
        $conditions[] = "(e.gender = '%s' OR e.gender = '%s')";
        $params[] = $male;
        $params[] = $femelle;
    } elseif (!empty($male)) {
        $conditions[] = "e.gender = '%s'";
        $params[] = $male;
    } elseif (!empty($femelle)) {
        $conditions[] = "e.gender = '%s'";
        $params[] = $femelle;
    }



    $sql = "SELECT e.first_name, e.last_name, e.emp_no, d.dept_name, d.dept_no,e.gender,
                   TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) as age
            FROM employees e
            JOIN dept_emp de ON de.emp_no = e.emp_no
            JOIN departments d ON d.dept_no = de.dept_no
            JOIN titles ti ON ti.emp_no = e.emp_no";

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY e.emp_no ORDER BY age ASC LIMIT %d OFFSET %d";
    $params[] = $limites;
    $params[] = $offset;

    $requete = vsprintf($sql, $params);
    $attente = mysqli_query(connection(), $requete);
    $table = [];

    while ($resultat = mysqli_fetch_assoc($attente)) {
        $table[] = $resultat;
    }
    return $table;
}

function nombreTotalCherche($age, $age1, $nom, $departement,$male,$femelle) {
    $conditions = [];
    $params = [];

    if (!empty($departement) && $departement !== "0") {
        $conditions[] = "d.dept_no = '%s'";
        $params[] = $departement;
    }

    if ($age > 0) {
        $conditions[] = "TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) >= %d";
        $params[] = $age;
    }

    if ($age1 > 0) {
        $conditions[] = "TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) <= %d";
        $params[] = $age1;
    }

    if (!empty($nom)) {
        $conditions[] = "(LOWER(e.first_name) LIKE LOWER('%%%s%%') OR LOWER(e.last_name) LIKE LOWER('%%%s%%'))";
        $params[] = $nom;
        $params[] = $nom;
    }

    if (!empty($male) && !empty($femelle)) {
        $conditions[] = "(e.gender = '%s' OR e.gender = '%s')";
        $params[] = $male;
        $params[] = $femelle;
    } elseif (!empty($male)) {
        $conditions[] = "e.gender = '%s'";
        $params[] = $male;
    } elseif (!empty($femelle)) {
        $conditions[] = "e.gender = '%s'";
        $params[] = $femelle;
    }


    $sql = "SELECT COUNT(DISTINCT e.emp_no) AS total
            FROM employees e
            JOIN dept_emp de ON de.emp_no = e.emp_no
            JOIN departments d ON d.dept_no = de.dept_no
            JOIN titles ti ON ti.emp_no = e.emp_no";

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $requete = vsprintf($sql, $params);
    $attente = mysqli_query(connection(), $requete);
    $resultat = mysqli_fetch_assoc($attente);

    return (int)$resultat['total'];
}


function EmployerMaleParDepartement($indice, $page, $parPage){
    $offset = ($page - 1) * $parPage;
    $requete="SELECT * from v_employees_deptemp_departements WHERE dept_no='%s' and gender='M' limit %d,%d";
    $requete=sprintf($requete,$indice,$offset, $parPage);
    $attente=mysqli_query(connection(),$requete);
    $tables=array();
    while($resultat=mysqli_fetch_assoc($attente)){
        $tables[]=$resultat;
    } 
    return $tables;
}

function NombreEmployesMaleParDepartement($indice) {
    $requete = "SELECT COUNT(*) AS total FROM v_employees_deptemp_departements WHERE dept_no = '%s'";
    $requete = sprintf($requete, $indice);
    $attente = mysqli_query(connection(), $requete);
    $resultat = mysqli_fetch_assoc($attente);
    return (int)$resultat['total'];
}
function EmployerfemelleParDepartement($indice, $page, $parPage){
    $offset = ($page - 1) * $parPage;
    $requete="SELECT * from v_employees_deptemp_departements WHERE dept_no='%s' and gender='F' limit %d,%d";
    $requete=sprintf($requete,$indice,$offset, $parPage);
    $attente=mysqli_query(connection(),$requete);
    $tables=array();
    while($resultat=mysqli_fetch_assoc($attente)){
        $tables[]=$resultat;
    } 
    return $tables;
}

function NombreEmployesfemelleParDepartement($indice) {
    $requete = "SELECT COUNT(*) AS total FROM v_employees_deptemp_departements WHERE dept_no = '%s'";
    $requete = sprintf($requete, $indice);
    $attente = mysqli_query(connection(), $requete);
    $resultat = mysqli_fetch_assoc($attente);
    return (int)$resultat['total'];
}


function salaire_moyen($indice){
    $requete="SELECT AVG(s.salary) as salaire_moyen FROM salaries s JOIN dept_emp dep ON dep.emp_no = s.emp_no WHERE dep.dept_no = '%s'";
    $requete=sprintf($requete,$indice);
    $attente=mysqli_query(connection(),$requete);
    $resultat=mysqli_fetch_assoc($attente);
    return $resultat['salaire_moyen'];
}

function historique_emploie($indice){
    $requete="SELECT * from v_info_titre where emp_no='%d' group by title";
    $requete=sprintf($requete,$indice);
    $attente=mysqli_query(connection(),$requete);
    $table=array();
    while($resultat=mysqli_fetch_assoc($attente)){
        $table[]=$resultat;
    }
    return $table;
}


function changer_departement($emp_no,$departement,$date){
    $connecteur=connection();
    $requete1="UPDATE dept_emp set to_date='%s' where emp_no='%d' and to_date='9999-01-01'";
    $requete1=sprintf($requete1,$date,$emp_no);
    $attente1=mysqli_query($connecteur,$requete1);

    $requete2="INSERT into dept_emp(emp_no,dept_no,from_date,to_date) values('%d','%s','%s','9999-01-01')";
    $requete2=sprintf($requete2,$emp_no,$departement,$date);
    $attente2=mysqli_query($connecteur,$requete2);
}

?>