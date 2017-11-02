VERSION 1.4 :

    - Possibilité d'exécution d'une requête en interne au serveur via la methode \Webservices\RequestManager::execute($requestCMD, $arrayRequestsTypes)
    - Recupération des données des types PUT et DELETE + protection contre l'insertion HTML dans des tableaux statiques.
    - Possibilité de push des variables dans le tableau statiques d'un type de requête.

VERSION 1.3 :

    - Correction dans la gestion des namespaces, permet d'avoir tout type de namespace (1.2: limité à un namespace à 2 éléments).
    - Gestion des noms composés pour la requête.
    - Interdiction d'accès aux dossiers relatifs au projet (Config, Requete, Webservices) et autorise seulement l'index.
    - Ajout d'un champs "keys" dans config.ini pour crée un system de clé par rapport à l'accès d'une action
    - l'action GET étant l'action la plus faible de droit, Si on demande un GET en même temps qu'une autre action et que
      la clé de l'autre action à été validé, l'action GET sera validé sans clé.
    - Ajout de methode dans le RequestController
        *   getDatas() => Récupère le contenu des données envoyée par la requête selon son type.
                                    Si le type de la requête est GET, on récupère le contenu de $_GET avec la protection "insertion HTML"

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