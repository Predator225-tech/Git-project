<?php
    require('../inc/fonction.php');
    session_start();
    $actes=prendreToutVideo();
    $_SESSION['id_utilisateur'];
    $_SESSION['mail'];
    $_SESSION['pseudo'];
    //echo "utilisateur connecter en tant que ". $_SESSION['pseudo'];

    $totalVideos = count($actes);
    
    // Gestion de l'index courant (GET ?index=)
    $currentIndex = isset($_GET['index']) ? intval($_GET['index']) : 0;
    $currentIndex = max(0, min($currentIndex, $totalVideos - 1)); // Sécurité
    
    // Calcul des index précédent et suivant
    $prevIndex = ($currentIndex - 1 + $totalVideos) % $totalVideos;
    $nextIndex = ($currentIndex + 1) % $totalVideos;
    
    $video = $actes[$currentIndex];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>short tiktok</title>
    <style>
        body {
      margin: 0;
      padding: 0;
      background: black;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      font-family: Arial, sans-serif;
    }
    video {
      max-width: 100%;
      max-height: 80%;
      border-radius: 10px;
    }
    .nav-links {
      margin-top: 15px;
    }
    .nav-links a {
      margin: 0 10px;
      padding: 10px 16px;
      background: white;
      color: black;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
    }
    .top-bar {
      position: absolute;
      top: 10px;
      right: 10px;
    }
    .top-bar a {
      color: white;
      margin-left: 10px;
      text-decoration
    }
    </style>
</head>
<body>
<span>Connecté en tant que <?= $_SESSION['pseudo'] ?></span>
        <div class="video-section">
            <video controls autoplay muted playsinline loop>
                <source src="video_upload/<?= htmlspecialchars($video['contenue']) ?>" type="video/mp4">
                Votre navigateur ne supporte pas la lecture vidéo.
            </video>
        </div>

    <button type="button"><a href="deconexion.php">Déconnexion</a></button>
    <a href="compte.php?indice=<?php echo $_SESSION['id_utilisateur']?>">votre compte</a>

    <div class="nav-links">
        <a href="?index=<?= $prevIndex ?>">⬆</a>
        <a href="?index=<?= $nextIndex ?>">⬇</a>
        <a href="commentaire.php?indice=<?=$acte['id']?>">💬 Commentaire</a>
    </div>
</body>
</html>