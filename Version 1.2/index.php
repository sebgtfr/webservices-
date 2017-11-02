<?php

/* Chemin de la base du serveur */
define('__ROOTDIR__', substr($_SERVER['SCRIPT_FILENAME'], 0, strlen($_SERVER['SCRIPT_FILENAME']) - strlen(strrchr($_SERVER['SCRIPT_FILENAME'], '/'))));

function                                            genPathClass($classname)
{
        $aClass = explode('\\', $classname);
        return ( __ROOTDIR__ . ((count($aClass) == 2) ? ("/$aClass[0]/$aClass[1]/$aClass[1].php") : ("/Objects/$aClass[0]/$aClass[0].php")));
}

/* autoload des classes */
spl_autoload_register(function ($classname)
{
        if (file_exists($pathClassFile = genPathClass($classname)))
        {
                require_once $pathClassFile;
        }
        else
        {
                $errorResponse = new \Webservices\Response();
                $aClass = explode('\\', $classname);
                $errorResponse->setError('404/' . end($aClass));
                echo utf8_encode(json_encode($errorResponse->getError()));
                exit();
        }
});

function                                            initConfig()
{
        $iniConf = array
        (
                'config' => __ROOTDIR__ . '/Config/config.ini',
                'database' => __ROOTDIR__ . '/Config/database.ini'
        );
        
        $aEnv = array('dev', 'prod');
        if ((pathinfo($iniConf['config'], PATHINFO_EXTENSION) == 'ini') && (file_exists($iniConf['config'])) &&
                (($dataIniFile = parse_ini_file($iniConf['config'], true))))
        {
                if (array_key_exists('config', $dataIniFile))
                {
                        define('__ENV__', (array_key_exists('env', $dataIniFile['config']) ? strtolower($dataIniFile['config']['env']) : false));
                }
                if (array_key_exists('errors', $dataIniFile))
                {
                        \Webservices\Response::loadErrorFormat($dataIniFile['errors']);
                }
        }

        if (!defined('__ENV__') || !in_array(__ENV__, $aEnv))
        {
                echo 'Erreur dans la configuration du webservice !';
                die();
        }

        if ((pathinfo($iniConf['database'], PATHINFO_EXTENSION) == 'ini') && (file_exists($iniConf['database'])) &&
                (($dataIniFile = parse_ini_file($iniConf['database'], true))))
         {
                $envLen = strlen(__ENV__);
                foreach ($dataIniFile as $sectionName => $sectionValues)
                {
                        if (substr($sectionName, 0, $envLen) == __ENV__) /* Base de données appartenant à l'environnement */
                        {
                                $instanceName = lcfirst(substr($sectionName, $envLen));
                                if ((($instance = \Webservices\Database::getInstance($instanceName)) != null))
                                {
                                        $instance->initByArray($sectionValues);
                                }
                        }
                }
         }
        
}

initConfig();
 
$requestManager = new \Webservices\RequestManager();

if (empty(($answerRequest = $requestManager->executeRequest())))
{
        $errorResponse = new \Webservices\Response();
        $errorResponse->setError('404/envoyée');
        $answerRequest =  $errorResponse->getError();
}

echo utf8_encode(json_encode($answerRequest));