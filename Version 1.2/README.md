################################ PECUNIA REST API ##############################

1) Les Requêtes.

L'API est capables de géré les 4 types de requête possible :

- GET => Récupère les données de la base de données
- POST => Ajoute un nouvelle élément dans la base de données
- PUT => Mets à jour la base de données, Si la donnée n'existe pas,
               l'API la créée.
- DELETE => Détruit une donnée de la base de données.

L'API est capable d'exécuté les 4 requêtes en même temps,
et retourne l'état des 4 requêtes à chaques appels.
L'architecture de retour est donc ainsi :

{
        "status" : {"success" | "error"}
        "GET"
        {
                "status" : {VALUE}
        }
        "POST"
        {
                "status" : {VALUE}
        }
        "PUT"
        {
                "status" : {VALUE}
        }
        "DELETE"
        {
                "status" : {VALUE}
        }
}

En cas de succès, le champs "status" = "success".
Par défault, le champs "status" = "error" avec le code 400,
mais si une erreur est détectée durant la requête, le champ
de la requête prend cette architecture.

{
        "status" : "error"
        "codeError" : {CODE_ERROR}
        "msgError" : {MSG_ERROR}
}

cf 5) Les codes erreurs

Il est possible d'accomplir en simultané n'importe quel requête avec la requête 'GET'.
pour se faire, il suffit d'envoyer le type de la requête en utilisant le header HTTP et
ajouté dans l'url, le prefixe "get" devant le nom du controlleur puis ajouté les paramètres
URL. Par exemple, pour faire une requête "POST" et "GET" au controlleur "client",
il faut envoyer la requête comme ceci (format jquery) :

$.ajax({
    type: 'POST'
    url : '{DOMAINE}/getclient?param1=value1&param2=value2'
});

Pour faire un "GET" seul au controlleur client, on peut :

$.ajax({
    type: 'GET'
    url : '{DOMAINE}/client'
    data =
    {
        "param1" : "value1"
        "param2" : "value2"
    }
});

2) Créer une nouvelle requête

    1 - Crée votre controlleur dans le repertoire /Request/{NOM_CONTROLLEUR}/{NOM_CONTROLLEUR}.php
        1.1 - {NOM_CONTROLLEUR} = nom du controlleur au format capitalisé.
    2 - Dans le controlleur, crée un objet {NOM_CONTROLLEUR} dans le namespace Request et héritant de \Webservices\RequestController
        2.1 - Crée les méthodes au nom du de la methodes d'envoie des données (get, post, put, delete).
        2.2 - Ces méthodes devront être prototypé de la manière suivante :

        public function                             get(&$response);
        public function                             post(&$response);
        public function                             put(&$response);
        public function                             delete(&$response);

        Votre objet de requête hérite de quelques fonctions permettant de récupérer des données :

        - getDatabase($idInstance) => Raccourci pour récupérer une instance de base de données.
        - getPath() => Permet de récupérer le chemin du repertoire contenant le script de votre objet.
        - getParams => Permet de récupérer un tableau contenant les paramètres ($x dans l'exemple) envoyer dans l'URL tel que {DOMAIN}/{REQUEST}/$1/$2/...?GET['Param1']='...'&...

3) Listes des requêtes.

TYPE = GET
LIEN = client?login={LOGIN}&password={PASSWORD}
DESCRIPTION = Récupères les informations utilisateurs d'un client
                         en passant par le nom et le mot de passe.

TYPE = GET
LIEN = taux
DESCRIPTION = Récupères les taux en cours.

4) La configuration

La configuration du webservice se fais dans le dossier "Config" à l'intérieur des fichiers
"config.ini" et "database.ini".

    4.1) Configuration général

    Le fichier "config.ini" contient la documentation général de l'API.
    Celle-ci comporte :
        - L'environnement (prend les valeurs 'dev' ou 'prod'
        - Les erreurs assigné par code. Celle-ci sont écrite sous forme de chaine formaté ou $1 = param1, etc... .
         Attention, '$$' = $ et $0 = {{CODE_ERROR}}.
         Ainsi on prépare une erreur ainsi Response $response->setError('{{CODE_ERROR}}/$1/$2/...');

    4.2) Les bases de données

    Le fichier "database.ini" contient les bases de données rataché à l'API.
    Chaques base de données possède sa section et dois suivre la norme suivante :

    [{{ENV}}{{Alias de l'instance}}]
    host = ''
    dbname = ''
    login = ''
    password = ''

    avec {{ENV}} correspondant à l'environnement de l'API (dev ou prod)
    et {{Alias de l'instance}}

    On peut ensuite utilisé la methode statique \Database::getInstance({{Alias de l'instance}})
    pour récupérer l'instance de la base de données.

    La première lettre de {{Alias de l'instance}} sera toujours en minuscule, ainsi :

    "prodDatabase" et "proddatabase" devient "database"
    "devDatabaseNumberTwo" devient "databaseNumberTwo"

5) Les codes erreurs

# Voir /Config/config.ini [errors] pour le controlle des erreurs. 

0 => Erreur inconnue.
100 => Erreur connexion ou d'exécution de la requête auprès de la base de données.
101 => Erreur aucune ligne retourné par la base de données.
400 => Erreur requête érronée
404 => Erreur requête inconnu.
504 => Erreur methode d'envoi des données inconnues.