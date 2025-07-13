<?php
require('../inc/fonction.php');
//session_start();
$id=$_SESSION['id_utilisateur'];
echo $id;
$Attentes=montreTout();

#traitement des commentaires
if($_SERVER['REQUEST_METHOD']=='POST'){
    if(isset($_POST['comment']) && isset($_POST['id_publication'])){
        $commentaire=$_POST['comment'];
        $id_publication=$_POST['id_publication'];
        prendreCommentaire($id,$commentaire,$id_publication);
        header("Location: " . $_SERVER['PHP_SELF']);#voici le truc magique qui te ramenera ici sans que le foutoir ne t'envoie sur une page blanche ou tu dois faire un retour pour revenir ici
        exit();
    }
    # traitement des likes
    if(isset($_POST['like']) && isset($_POST['id_publication2'])) {
        like($_SESSION['id_utilisateur'], $_POST['id_publication2']);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
}
}

//$nombredeLike=compterLike();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Accueil | MiniTumblr</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #0d0d0d;
            color: white;
            display: flex;
        }

        .sidebar {
            width: 200px;
            background: #0a0a0a;
            padding: 20px;
            height: 100vh;
        }

        .sidebar h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar li {
            margin: 12px 0;
            cursor: pointer;
        }

        .main {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .post {
            background-color: #1c1c1c;
            margin-bottom: 25px;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 0 8px rgba(0, 255, 255, 0.05);
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .author {
            font-weight: bold;
            color: #00ffff;
        }

        .post img,
        .post video {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 6px;
        }

        .description {
            margin: 10px 0;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .like-btn {
            background: none;
            border: none;
            color: #00ffff;
            cursor: pointer;
            font-size: 16px;
        }

        .comments {
            margin-top: 15px;
            border-top: 1px solid #333;
            padding-top: 10px;
        }

        .comment {
            margin-bottom: 8px;
        }

        .comment strong {
            color: #00ffff;
        }

        .comment-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        .comment-form input {
            padding: 8px;
            background-color: #2a2a2a;
            border: 1px solid #444;
            border-radius: 5px;
            color: white;
        }

        .comment-form button {
            align-self: flex-start;
            padding: 8px 12px;
            background-color: #00ffff;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .comment-form button:hover {
            background-color: #00cccc;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>tumblr</h2>
        <ul>
            <li><a href="#">🏠</a></li>
            <li><a href="upload.php">📝</a>Uploader</li>
            <li><a href="profil.php">📝</a>Mon Profil</li>
            <li><a href="deconnection.php">🚪</a>Déconnexion</li>
        </ul>
    </div>


    <!-- publication de tout les uploads -->   
    <div class="main">

                      
                <?php foreach($Attentes as $Attente) {?>
                    <div class="post">
                        <div class="post-header">
                            <div class="author"><?=$Attente['mail']?></div>
                        <div>publié le <?=$Attente['publication']?></div>
                    </div>

                        <!-- affichage des vidéos/photos en fonction des mimes -->
                        <?php if($Attente['mediatype']=='video') {?>
                            <video controls>
                                <source src="media/<?=$Attente['chemin']?>" type="<?=$mime?>">
                            </video>
                            <div class="media-description"><?=$Attente['titre']?></div>
                        <?php }else{?>
                            <img src="media/<?=$Attente['chemin']?>" alt="<?=$Attente['id']?>">
                            <div class="media-description"><?=$Attente['titre']?></div>
                        <?php }?>


                        <!-- bouton des likes -->
                        <div class="actions">
                            <form action="<?= $_SERVER['PHP_SELF']?>" method="post">
                                <?php var_dump($Attente['id']); 
                                    $nombredeLike=compterLike($Attente['id']);
                                ?>
                                <input type="hidden" name="id_publication2" value="<?=$Attente['id']?>">
                                <button class="like-btn" type="submit" name="like">J'aime <?=$nombredeLike?></button>
                            </form>
                        </div> 

                        <!-- bloc des commentaire (affichage) -->
                        <div class="comments">
                            <?php $Commentaires=afficherCommentaire($Attente['id']);
                            foreach($Commentaires as $commentaire){?>
                                <div class="comment"><strong><?=$commentaire['mail'] ?></strong> : <?=$commentaire['contenue'] ?></div>
                            <?php }?>
                            <!-- Formulaire de commentaire -->
                            <form class="comment-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                                <input type="hidden" name="id_publication" value="<?=$Attente['id']?>">
                                <input type="text" name="comment" placeholder="Ajouter un commentaire..." required>
                                <button type="submit">Envoyer</button>
                            </form>
                        </div>



                    </div>
                <?php }?>
            </div>
        </div>
    </div>

</body>

</html>