<?php
require('../inc/fonction.php');
/*include('uploadable.php');
session_start();*/
if($_SERVER['REQUEST_METHOD']=='POST'){
  $titre=$_POST['title'];
  $description=$_POST['description'];
  $_SESSION['titre']=$titre;
  $_SESSION['description']=$description;
}



/*Place pour le upload*/
ini_set('display_errors', 1);
error_reporting(E_ALL);
//session_start();
//require('../inc/fonction.php');

$uploadDir = __DIR__ . '/media/';
$maxSize = 66 * 1024 * 1024; // 66 Mo
$allowedMimeTypes = [
    'image/jpeg'=>'photo',
    'image/png'=>'photo', 
    'image/gif'=>'photo',
    'video/mp4'=>'video',
    'video/webm'=>'video',
    'video/ogg'=>'video'
];

// Vérifie si un fichier est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichier'])) {
    $file = $_FILES['fichier'];

    //vérifie les erreurs de upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        //die('Erreur lors de l’upload : ' . $file['error']);
        $_SESSION['erreur_upload']=" <br> Erreur lors de l’upload : ". $file['error'];
        header('Location: Accueil.php');
        exit();
    }

    // Vérifie la taille
    if ($file['size'] > $maxSize) {
        //die('Le fichier est trop volumineux.');
        $_SESSION['taille']=" <br> Le fichier est trop volumineux.";
        header('Location: Accueil.php');
        exit();
    }


    // Vérifie le type MIME avec `finfo`
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!array_key_exists($mime, $allowedMimeTypes)) {
        //die('Type de fichier non autorisé : ' . $mime);
        $_SESSION['type']=" <br> Type de fichier non autorisé : " . $mime;
        header('Location: Accueil.php');
            exit();
    }
    $type=$allowedMimeTypes[$mime];

    // renommer le fichier
    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = $originalName . '_' . uniqid() . '.' . $extension;
    
    
    // Déplace le fichier
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
        //echo "Fichier uploadé avec succès : ". $newName;
        $_SESSION['Nomnouveau']=$newName;
        $_SESSION['succes']=" <br> Fichier uploadé avec succès : ". $newName;
        insererDansMedia($_SESSION['id_utilisateur'],$newName,$type,$_SESSION['titre'],$_SESSION['description']);
            header('Location: profil.php');
            exit();
        //montreParID($_SESSION['id']);
    } 
    else {
        //echo "Échec du déplacement du fichier.";
        $_SESSION['echec']=" <br> Échec du déplacement du fichier.";
        header('Location: profil.php');
            exit();
    }
}
/*else {
    echo "Aucun fichier reçu.";
}
insererDansPhoto($_SESSION['id_utilisateur'],$_SESSION['Nomnouveau']);
*/

/*header('Location: Accueil.php');
exit();
*/
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nouvelle publication | MiniTumblr</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #0d0d0d;
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .upload-container {
      background-color: #1c1c1c;
      padding: 40px;
      border-radius: 12px;
      width: 100%;
      max-width: 500px;
      box-shadow: 0 0 15px rgba(0, 255, 255, 0.1);
    }

    .upload-container h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #00ffff;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      color: #ccc;
    }

    .form-group input[type="text"],
    .form-group textarea,
    .form-group input[type="file"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #444;
      border-radius: 6px;
      background-color: #2a2a2a;
      color: white;
      font-size: 15px;
    }

    .form-group input[type="file"] {
      padding: 6px;
    }

    .form-group textarea {
      resize: vertical;
      height: 80px;
    }

    .btn-upload {
      width: 100%;
      padding: 12px;
      background-color: #00ffff;
      color: black;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-upload:hover {
      background-color: #00cccc;
    }

    .back-link {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
    }

    .back-link a {
      color: #00ffff;
      text-decoration: none;
    }

    .back-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="upload-container">
    <h2>Nouvelle publication</h2>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="title">Titre</label>
        <input type="text" id="title" name="title" placeholder="Titre de votre post" required>
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" placeholder="Décrivez votre image ou vidéo..."></textarea>
      </div>
      <div class="form-group">
        <label for="fichier">Fichier (image ou vidéo)</label>
        <input type="file" id="fichier" name="fichier" enctype="image/*,video/*" required>
      </div>
      <button type="submit" class="btn-upload">Publier</button>
    </form>
    <div class="back-link">
      <a href="home.php">⬅ Retour </a>
    </div>
  </div>

</body>
</html>