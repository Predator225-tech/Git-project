<?php

function enregistre_client($nom,$prenom,$date,$genre){
    $connex=connection();
    $requete="INSERT INTO immo_clients(nom,prenom,date_naissance,genre) VALUES ('%s','%s','%s','%s')";
    $requete=sprintf($requete,$nom,$prenom,$date,$genre);
    $attente=mysqli_query($connex,$requete);
    if ($attente) {
         
        $id_client = mysqli_insert_id($connex);  
        $_SESSION['id_client'] = $id_client;
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        header("Location: index.php");
        exit();
    } else {
        echo "Erreur lors de l'enregistrement de l'utilisateur.";
        header('location:index.php') ;
        exit();
    }
}
?>