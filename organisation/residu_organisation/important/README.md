note pour le createur 
quoique disent les professeurs ou ton binome s'il en existe et bien lorsque tu crées un fichier n'oublie jamais 
au grand JAMAIS de mettre les session_start() sinon:
    * il affichera toujours fatal error ou bien ce message iconique :

        "Il semble y avoir un problème sur ce site

        Il est possible que http://localhost/organisation/page/index.php connaisse un problème temporaire ou ait été déplacé.

        Code d’erreur : 500 Internal Server Error

        Le site est peut-être temporairement indisponible ou surchargé. Réessayez plus tard ;"
    
    * une page blanche qui ne te mets pas les raisons de cette foutue page blanche d'où la mise en place de ceci

    <?php 
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    ?>
    traduction :"montre moi ce p****(excusez moi mais c'est le mot juste) d'erreur ou sinon je jette mon ordinateur. Oh et si vous entendez dans le journal que j'ai été abattu pour meurtre témoigner en ma faveur en disant que c'est à cause de ce foutu session_start que tout a commencer "
    
    merci de votre attention à ceux et celles qui liront ce message et n'oublier pas 
    LISEZ ATTENTIVEMENT CE README SURTOUT QUAND C'EST EN FRANÇAIS CELA VOUS ÉVITERA BEAUCOUP DE PROBLÈME mais bref je vous ai mis les significations de tout les fichier à leurs titres alors bonne soirée ou journée à vous

Post Scriptum:
"veuiller supprimer le dossier résidu_organisation après utilisation "