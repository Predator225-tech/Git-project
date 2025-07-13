<?php
/* fonction de recherche pour le cas en génerale de recherche sans remplir tout les champs*/
function cherche($age, $age1, $nom, $departement, $limites, $offset) {
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

    $sql = "SELECT e.first_name, e.last_name, e.emp_no, d.dept_name, d.dept_no,
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

function nombreTotalCherche($age, $age1, $nom, $departement) {
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

/*limite de la fonction de recherche*/

?>