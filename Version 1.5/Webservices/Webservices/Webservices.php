<?php

class                                               Webservices
{
        /*
        ** ****** CONST ****** **
        */
        const                                       __TYPE_REQUEST__ = array
        (
                'GET',
                'POST',
                'PUT',
                'DELETE'
        );
    
        /* GENERAL INSTANCE */
        static public                               $_instance = null;
        static private                              $_lastID = 0;
        static private                              $_pathCookies = './';
        
        /*
        ** ****** ATTRIBUTS ****** **
        */
        private                                     $_id;
        private                                     $_datas = array();
        private                                     $_keepConnection = true;
        private                                     $_sessionCookiesUrl = array();
        private                                     $_options = array
        (
                CURLOPT_CUSTOMREQUEST => 'GET', // Type de la requête.
                CURLOPT_RETURNTRANSFER => true, // N'affiche pas le resultat, le recupère uniquement.
                CURLOPT_COOKIESESSION => true, // Redemarre une session de cookie en ignorant les anciens.
                CURLOPT_HEADER => false, // N'affiche pas le header HTTP dans la reponse.
                CURLOPT_FOLLOWLOCATION => true, // Suivre les redirections
                CURLOPT_MAXREDIRS => 1 // Nombre de redirection autorisé.
        );
        
        public function                             __construct($url = false, $typeRequest = 'GET', $keepConnection = true)
        {
                $this->_id = self::$_lastID++;
                $this->setURL($url);
                $this->_typeRequest = $this->setTypeRequest($typeRequest);
                $this->keepConnection($keepConnection);
        }
        
        public function                             __destruct()
        {
                foreach ($this->_sessionCookiesUrl as $fileCookieURL)
                {
                        if (is_file($fileCookieURL))
                        {
                                unlink($fileCookieURL);
                        }
                }
        }
        
        /*
        ** ****** ACCESSORS ****** **
        */
        public function                             setOption($option, $datas)
        {
                if (is_string($option) && !empty($option))
                {
                        $option = strtoupper($option);
                        if ($option != 'POSTFIELDS')
                        {
                                if (defined("CURLOPT_$option"))
                                {
                                        $this->_options[contant($option)] = $datas;
                                }
                        }
                        else
                        {
                                $this->pushDatas($datas);
                        }
                }
                return $this;
        }
        
        public function                             setURL($url)
        {
                if (is_string($url) && !empty($url))
                {
                        $this->_options[CURLOPT_URL] = $url;
                }
                return $this;
        }
        
        public function                             setTypeRequest($typeRequest)
        {
                if (is_string($typeRequest) && !empty($typeRequest))
                {
                        $typeRequest = strtoupper($typeRequest);
                        if (in_array($typeRequest, self::__TYPE_REQUEST__))
                        {
                                $this->_options[CURLOPT_CUSTOMREQUEST] = $typeRequest;
                        }
                }
                return $this;
        }
        
        public function                             keepConnection($keepConnection)
        {
                if (is_bool($keepConnection))
                {
                        $this->_keepConnection = $keepConnection;
                }
                return $this;
        }
        
        public function                             pushDatas()
        {
                $argc = func_num_args();
                switch ($argc)
                {
                        case 1:
                                $aDatas = func_get_arg(0);
                                if (is_array($aDatas))
                                {
                                        $this->_datas = array_merge($this->_datas, $aDatas);
                                }
                        break;
                        case 2:
                                $argv = func_get_args();
                                if ($argv[0] != null)
                                {
                                        $this->_datas[$argv[0]] = $argv[1];
                                }
                        break;
                        default:
                        break;
                }
                return $this;
        }
        
        public function                             clearDatas()
        {
                unset($this->_datas);
                $this->_datas = array();
                return $this;
        }
        
        /*
        ** ****** METHODS ****** **
        */
        public function                             execute($clearDatas = true)
        {
                if (array_key_exists(CURLOPT_URL, $this->_options))
                {
                        $fileCookieURL = null;
                        
                        /* Prepare les options issue du type de la requête.  */
                        $typeRequest = $this->_options[CURLOPT_CUSTOMREQUEST];
                        $prepareRequestDatas = "prepareRequest$typeRequest";
                        $this->$prepareRequestDatas();
                        
                        /* Sauvegarde de la session si on souhaite garder la connection */
                        $host = self::getHost($this->_options[CURLOPT_URL]);
                        if ($this->_keepConnection == true)
                        {
                                if (empty($fileCookieURL = $this->alreadyConnection($host)))
                                {
                                        $fileCookieURL = $this->genNameFileCookiesURL($host);
                                        if (!in_array($fileCookieURL, $this->_sessionCookiesUrl))
                                        {
                                                $this->_sessionCookiesUrl[$host] = $fileCookieURL;
                                                if (!file_exists($fileCookieURL))
                                                {
                                                        @touch($fileCookieURL);
                                                }
                                        }
                                }
                        }
                        else
                        {
                                if ($this->alreadyConnection($host))
                                {
                                        $this->deleteConnection($host);
                                }
                        }
                        if (array_key_exists(CURLOPT_COOKIEJAR, $this->_options))
                        {
                                unset($this->_options[CURLOPT_COOKIEJAR]);
                        }
                        if ($fileCookieURL != null && !empty($fileCookieURL) && file_exists($fileCookieURL))
                        {
                                $this->_options[CURLOPT_COOKIEJAR] = $fileCookieURL;
                        }
                        
                        /* Initialisation de la session */
                        $curlID = @curl_init();
                        
                        /* Configuration de la session */                        
                        curl_setopt_array($curlID, $this->_options);
                        
                        /* Execution */
                        $retDatas = curl_exec($curlID);
                        
                        /* fermeture de la session */
                        curl_close($curlID);
                        
                        if ($clearDatas == true)
                        {
                                $this->clearDatas();
                        }

                        return $retDatas;
                }
                return false;
        }
        
        static private function                     getHost($url)
        {
                $fullHostname = parse_url($url, PHP_URL_HOST);
                $hostname = (substr($fullHostname, 0, 4) == 'www.') ? substr($fullHostname, 4) : $fullHostname;
                $lenHost = strlen($hostname);
                $host = '';
                for ($i = 0; $i < $lenHost && $hostname[$i] != '.'; ++$i)
                {
                        $host .= $hostname[$i];
                }
                return $host;
        }
        
        public function                             alreadyConnection($host)
        {
                if (array_key_exists($host, $this->_sessionCookiesUrl) && $this->_sessionCookiesUrl[$host] != null && !empty($this->_sessionCookiesUrl[$host]))
                {
                        return $this->_sessionCookiesUrl[$host];
                }
                return false;
        }
        
        private function                            genNameFileCookiesURL($host)
        {
                return (self::$_pathCookies . ".Webservices-{$this->_id}" . (empty($host) ? '' : "-$host") . '.txt');
        }
        
        public function                             deleteConnection($host)
        {
                if (file_exists($this->_sessionCookiesUrl[$host]))
                {
                        unlink($this->_sessionCookiesUrl[$host]);
                }
                unset($this->_sessionCookiesUrl[$host]);
        }
        
        /*
        ** ****** METHODES UTILISES POUR PREPARER L'EXECUTION D'UNE REQUETE SELON SON TYPE ****** **
        */
        private function                     prepareRequestGET()
        {
                $this->_options[CURLOPT_HTTPGET] = true;
                $this->_options[CURLOPT_URL] .= (empty(parse_url($this->_options[CURLOPT_URL], PHP_URL_QUERY))) ? '?' : '&';
                $this->_options[CURLOPT_URL] .= http_build_query($this->_datas);
        }
        
        private function                     prepareRequestPOST()
        {
                $this->_options[CURLOPT_POST] = true;
                $this->_options[CURLOPT_POSTFIELDS] = http_build_query($this->_datas);
        }
        
        private function                     prepareRequestPUT()
        {
                $httpHeader = array('X-HTTP-Method-Override: PUT');
                if (array_key_exists(CURLOPT_HTTPHEADER, $this->_options) && is_array($this->_options[CURLOPT_HTTPHEADER]))
                {
                        $httpHeader = array_merge($this->_options[CURLOPT_HTTPHEADER], $httpHeader);
                }
                
                $this->_options[CURLOPT_PUT] = true;
                $this->_options[CURLOPT_HTTPHEADER] = $httpHeader;
                $this->_options[CURLOPT_POSTFIELDS] = http_build_query($this->_datas);
        }
        
        private function                     prepareRequestDELETE()
        {
                $this->_options[CURLOPT_POSTFIELDS] = http_build_query($this->_datas);
        }
        
        /*
        ** ****** EXECUTION D'UNE INSTANCE SANS OBJET ****** **
        */
        static public function                      setPathCookie($pathCookie)
        {
                if (is_string($pathCookie) && !empty($pathCookie))
                {
                        $i = 0;
                        $len = strlen($pathCookie);
                        $path = '';
                        for (;$i < $len; ++$i)
                        {
                                if ($pathCookie[$i] == '/' && !empty($path) && !is_dir($path))
                                {
                                        @mkdir($path);
                                }
                                $path .= $pathCookie[$i];
                        }
                        if (!is_dir($path))
                        {
                                @mkdir($path);
                        }
                        self::$_pathCookies = $pathCookie;
                        if (self::$_pathCookies[$i - 1] != '/')
                        {
                                self::$_pathCookies .= '/';
                        }
                }
        }
        
        static public function                      getUniqueInstance()
        {
                if (\Webservices::$_instance == null)
                {
                        \Webservices::$_instance = new \Webservices();
                }
                return \Webservices::$_instance;
        }
        
        static public function                      executeRequest($url, $typeRequest = 'GET', $datas = array(), $keepConnection = true)
        {
                return \Webservices::getUniqueInstance()->setURL($url)->setTypeRequest($typeRequest)->pushDatas($datas)->keepConnection($keepConnection)->execute();
        }
}
