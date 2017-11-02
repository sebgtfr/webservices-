<?php

namespace                                           Webservices;

class                                                               Database
{
        /*
        ** Const
        */
        const                                                      PDOTYPEBIND = array('boolean' => \PDO::PARAM_BOOL,
                                                                                                          'integer' => \PDO::PARAM_INT,
                                                                                                          'string' => \PDO::PARAM_STR,
                                                                                                          'NULL' => \PDO::PARAM_NULL);
        
        /*
        ** Private static attribute
        */
        private static                                           $_instances = array();

        /*
        ** Private attribute
        */
        /* Datas Database */
        private                                                    $_motor = null;
        private                                                    $_host = null;
        private                                                    $_dbname = null;
        private                                                    $_login = null;
        private                                                    $_password = null;
        private                                                    $_port = null;
        private                                                    $_charset = null;

        /* Datas PDO */
        private                                                    $_pdo = null;
        private                                                    $_lastQuery = null;
        private                                                    $_request = null;
        private                                                    $_transaction = false;

        /*
       ** ctor
       */
        public function                                         __construct()
        {
        }
        
        /*
        ** Static methods
        */
        public static function                      getInstance($idInstance)
        {
                if (isset($idInstance))
                {
                        if (!array_key_exists($idInstance, self::$_instances))
                        {
                                self::$_instances[$idInstance] = new \Webservices\Database();
                        }
                        return (self::$_instances[$idInstance]);
                }
                return (null);
        }
        
        public static function                      instanceExist($idInstance)
        {
                return (array_key_exists($idInstance, \Webservices\Database::$_instances));
        }
        
        public static function                      initInstancesByIni($aIdInstanceIniSection, $iniFilename)
        {
                if (is_array($aIdInstanceIniSection) && !empty($aIdInstanceIniSection) &&
                        (is_string($iniFilename)) && (pathinfo($iniFilename, PATHINFO_EXTENSION) == 'ini') &&
                        (file_exists($iniFilename)) && (($dataIniFile = parse_ini_file($iniFilename, true))))
                {
                        foreach ($aIdInstanceIniSection as $idInstance => $iniSection)
                        {
                                if (array_key_exists($iniSection, $dataIniFile) && (($instance = \Webservices\Database::getInstance($idInstance)) != null))
                                {
                                        $instance->initByArray($dataIniFile[$iniSection]);
                                }
                        }
                }
                    
        }

        /*
        ** Accessor
        */
        public function                             setMotor($motor)
        {
                if (is_string($motor) && !empty($motor))
                {
                        $this->_motor = $motor;
                }
        }
                
        public function                                         setHost($host)
        {
                if (is_string($host) && !empty($host))
                {
                        $this->_host = $host;
                }
        }
                
        public function                                         setDbname($dbname)
        {
                if (is_string($dbname) && !empty($dbname))
                {
                        $this->_dbname = $dbname;
                }
        }
                
        public function                                         setLogin($login)
        {
                if (is_string($login) && !empty($login))
                {
                        $this->_login = $login;
                }
        }
                
        public function                                         setPassword($password)
        {
                if (is_string($password) && !empty($password))
                {
                        $this->_password = $password;
                }
        }
                
        public function                                         setCharset($charset)
        {
                if (is_string($charset) && !empty($charset))
                {
                        $this->_charset = $charset;
                }
        }
                
        public function                                         setPort($port)
        {
                if (is_string($port) && !empty($port))
                {
                        $this->_port = $port;
                }
        }
        
        public function                                         initByParams($host = null, $dbname = null, $login = null, $password = null, $motor = null, $charset = null, $port = null)
        {
                $this->setMotor($motor);
                $this->setHost($host);
                $this->setDbname($dbname);
                $this->setLogin($login);
                $this->setPassword($password);
                $this->setCharset($charset);
                $this->setPort($port);
        }
                
        public function                                         initByArray($aDatasConfig)
        {
                if (is_array($aDatasConfig))
                {
                        $indexes = array('host', 'dbname', 'login', 'password', 'motor', 'charset', 'port');
                        
                        foreach ($indexes as $index)
                        {
                                if (!array_key_exists($index, $aDatasConfig))
                                {
                                        $aDatasConfig[$index] = null;
                                }
                        }                        
                        $this->initByParams($aDatasConfig['host'], $aDatasConfig['dbname'], $aDatasConfig['login'],
                                                        $aDatasConfig['password'], $aDatasConfig['motor'], $aDatasConfig['charset'],
                                                        $aDatasConfig['port']);
                }
        }

        public function                                         initByIni($iniFilename, $iniSection)
        {
                if ((is_string($iniFilename)) && (pathinfo($iniFilename, PATHINFO_EXTENSION) == 'ini') &&
                    (file_exists($iniFilename)) && (($dataIniFile = parse_ini_file($iniFilename, true))) &&
                    (is_string($iniSection) && array_key_exists($iniSection, $dataIniFile)))
                {
                        $this->initByArray($dataIniFile[$iniSection]);
                }
        }
        
        public function                                         setAccount()
        {
                $argc = func_num_args();
                $argv = func_get_args();

                switch ($argc)
                {
                        case 1:
                                $this->initByArray($argv[0]);
                        break;
                        case 2:
                                $this->initByIni($argv[0], $argv[1]);
                        break;
                        default:
                                if ($argc <= 7)
                                {
                                        for ($i = $argc; $i < 7; ++$i)
                                        {
                                                $argv[$i] = null;
                                        }
                                        $this->initByParams($argv[0], $argv[1], $argv[2], $argv[3], $argv[4], $argv[5], $argv[6]);
                                }
                        break;
                }
        }
        
        public function                                         connect()
        {
                if (!$this->isConnect())
                {
                        if (isset($this->_host) && isset($this->_dbname) && isset($this->_login))
                        {
                                $dns = (((isset($this->_motor)) ? $this->_motor : 'mysql') . ':host=' . $this->_host .
                                            ';dbname=' . $this->_dbname . ';charset=' . ((isset($this->_charset)) ? $this->_charset : 'utf8'));
                                if (isset($this->_port))
                                {
                                        $dns .= ';port=' . $this->_port;
                                }
                                try
                                {
                                        $this->_pdo = new \PDO($dns, $this->_login, $this->_password);
                                        return true;
                                }
                                catch (\Exception $ex)
                                {
                                        throw $ex;
                                }
                        }
                }
                return false;
        }
        
        public function                                         disconnect()
        {
                if ($this->isConnect())
                {
                        $this->_pdo = null;
                        $this->_lastQuery = null;
                }
        }
        
        public function                                         isConnect()
        {
                return isset($this->_pdo);
        }

        public function                                         beginTransaction()
        {
                if ($this->_transaction == false)
                {
                        $this->connect();
                        $this->_pdo->beginTransaction();
                        $this->_transaction = true;
                }
        }
        
        public function                                         endTransaction($success)
        {
                if ($this->isConnect() && $this->_transaction == true)
                {
                        if ($success)
                        {
                                $this->_pdo->commit();
                        }
                        else
                        {
                                $this->_pdo->rollback();
                        }
                        $this->_transaction = false;
                }
        }
        
        public function                                         setQuery($query)
        {
                if (is_string($query) && !empty($query))
                {
                        if (!$this->isConnect())
                        {
                                if (!$this->connect())
                                {
                                        return false;
                                }
                        }
                        if ((!isset($this->_lastQuery)) || ($this->_lastQuery != $query))
                        {
                                $this->_request = $this->_pdo->prepare($query);
                                $this->_lastQuery = $query;
                        }
                }
        }
        
        public function                                         prepareQuery($query, $arrayMacroValue = array())
        {
                if (is_string($query) && !empty($query))
                {
                        try
                        {
                                $this->setQuery($query);
                                return $this->executeLastPrepareQuery($arrayMacroValue);
                        }
                        catch (\Exception $ex)
                        {
                                throw new \Exception('100');
                        }
                }
                return false;
        }
        
        public function                                         prepareQueryBySQLFile($sqlFile, $arrayMacroValue = array())
        {
                if ((pathinfo($sqlFile, PATHINFO_EXTENSION) == 'sql') && file_exists($sqlFile))
                {
                        return $this->prepareQuery(file_get_contents($sqlFile), $arrayMacroValue);
                }
                return false;
        }
        
        public function                                         executeLastPrepareQuery($arrayMacroValue = array())
        {
                if (isset($this->_lastQuery))
                {
                        if (!empty($arrayMacroValue))
                        {
                                foreach ($arrayMacroValue as $macro => $value)
                                {
                                        $this->_request->bindValue($macro, "$arrayMacroValue[$macro]", self::PDOTYPEBIND[gettype($value)]);
                                }
                        }
                        return $this->_request->execute();
                }
        }

        public function                                         getQueryLine($fetch = \PDO::FETCH_ASSOC)
        {
                return ((isset($this->_request)) ? $this->_request->fetch($fetch) : false);
        }
        
        /*
        ** @Description : Récupère toutes les lignes SQL de l'objet PDO dans un tableau bidimensionnel donc la première dimension correspond à une ligne complète
        **                        et la seconde au contenu de la ligne dont les clés sont les titres des colonnes SQL.
        */
        public function                                         getAllQueryLine($throw = true, $fetch = \PDO::FETCH_ASSOC)
        {
                if (empty($queryLines =  ((isset($this->_request)) ? $this->_request->fetchAll($fetch) : false)) && $throw)
                {
                        throw new \Exception('101');
                }
                return $queryLines;
                
        }
        
        public function                                         lastInsertID()
        {
                if ($this->isConnect())
                {
                        return $this->_pdo->lastInsertId();
                }
                return false;
        }
}