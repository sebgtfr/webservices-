VERSION 1.2 :

    -   Création de namespace afin de différencier les différents objets (Objet du webservice, des requêtes ou de l'utilisateur).
    -   Création d'un objet RequestController avec fonctions interne à la requête :
        *   getDatabase($idInstance) => Raccourci pour récupérer une instance de base de données.
        *   getPath() => Récupère le chemin du repertoire du script.
        *   getParams() => Récupère les paramètres envoyé dans le lien ($x) tel que : {DOMAIN}/{REQUEST}/$1/$2/...?$_GET[...]=...&...

VERSION 1.1 :

    -   Ajout fichier de configuration général (config.ini) avec configuration de l'environnement et des erreurs.
    -   Détection automatique des instances de base de données selon l'environnement dans database.ini
        permettant d'éviter les manipulations de code dans l'index.php.
    -   Envoi des erreurs de la base de données par exception.

VERSION 1.0 :

    *   Création de l'API de base.

    -   Gestion automatique des requêtes et des types de transfert de données (GET, POST, PUT, DELETE).
    -   Fichier de configuration des instances de base de données (database.ini) avec intégrations dans l'index.php.
    -   Gestion automatique de la génération de la réponse.