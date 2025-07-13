<?php
require('../inc/fonction.php');
session_start();
$date=$_POST['date'];
$departement=$_POST['departement'];
$emp_no=$_SESSION['index'];
if ($date && $departement !== '0' && $emp_no) {
    changer_departement($emp_no, $departement, $date);
    header("Location: fiche.php?indices=$emp_no");
    exit();
} else {
    header("Location: fiche.php?indices=$emp_no");
    exit();
}

?>