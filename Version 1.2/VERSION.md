VERSION 1.2 :

    -   Cr�ation de namespace afin de diff�rencier les diff�rents objets (Objet du webservice, des requ�tes ou de l'utilisateur).
    -   Cr�ation d'un objet RequestController avec fonctions interne � la requ�te :
        *   getDatabase($idInstance) => Raccourci pour r�cup�rer une instance de base de donn�es.
        *   getPath() => R�cup�re le chemin du repertoire du script.
        *   getParams() => R�cup�re les param�tres envoy� dans le lien ($x) tel que : {DOMAIN}/{REQUEST}/$1/$2/...?$_GET[...]=...&...

VERSION 1.1 :

    -   Ajout fichier de configuration g�n�ral (config.ini) avec configuration de l'environnement et des erreurs.
    -   D�tection automatique des instances de base de donn�es selon l'environnement dans database.ini
        permettant d'�viter les manipulations de code dans l'index.php.
    -   Envoi des erreurs de la base de donn�es par exception.

VERSION 1.0 :

    *   Cr�ation de l'API de base.

    -   Gestion automatique des requ�tes et des types de transfert de donn�es (GET, POST, PUT, DELETE).
    -   Fichier de configuration des instances de base de donn�es (database.ini) avec int�grations dans l'index.php.
    -   Gestion automatique de la g�n�ration de la r�ponse.