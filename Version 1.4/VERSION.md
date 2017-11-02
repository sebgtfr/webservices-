VERSION 1.4 :

    - Possibilit� d'ex�cution d'une requ�te en interne au serveur via la methode \Webservices\RequestManager::execute($requestCMD, $arrayRequestsTypes)
    - Recup�ration des donn�es des types PUT et DELETE + protection contre l'insertion HTML dans des tableaux statiques.
    - Possibilit� de push des variables dans le tableau statiques d'un type de requ�te.

VERSION 1.3 :

    - Correction dans la gestion des namespaces, permet d'avoir tout type de namespace (1.2: limit� � un namespace � 2 �l�ments).
    - Gestion des noms compos�s pour la requ�te.
    - Interdiction d'acc�s aux dossiers relatifs au projet (Config, Requete, Webservices) et autorise seulement l'index.
    - Ajout d'un champs "keys" dans config.ini pour cr�e un system de cl� par rapport � l'acc�s d'une action
    - l'action GET �tant l'action la plus faible de droit, Si on demande un GET en m�me temps qu'une autre action et que
      la cl� de l'autre action � �t� valid�, l'action GET sera valid� sans cl�.
    - Ajout de methode dans le RequestController
        *   getDatas() => R�cup�re le contenu des donn�es envoy�e par la requ�te selon son type.
                                    Si le type de la requ�te est GET, on r�cup�re le contenu de $_GET avec la protection "insertion HTML"

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