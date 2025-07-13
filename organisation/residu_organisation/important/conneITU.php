<?php
function connection(){
    static $connection=null;
    if ($connection===null) {
        $connection=mysqli_connect('localhost','ETU004034','vWOFc71U','db_s2_ETU004034');
        #(pour le rendu du devoir  'localhost','ETU004034','ton mot de passe','nom de la base de donner creer par le serveur')
        
        if(!$connection){

            die('Erreur de connexion à la base de donnés: '.mysqli_connect_error());
        }

        mysqli_set_charset($connection, 'utf8mb4');
    }

    return $connection;
}
?>