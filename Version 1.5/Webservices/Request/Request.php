<?php

namespace                                           Webservices;

class                                               Request
{
        const                                       __TYPES__ = array('GET', 'POST', 'PUT', 'DELETE');
        static private                              $_keys = array();
        static private                              $_datas = array();
        static private                              $_datasInit = array();
    
        private                                     $_type;
        private                                     $_response;
        
        public function                             __construct($type)
        {
                $this->_response = new \Webservices\Response();
                if (is_string($type) && ($type = strtoupper($type)) && in_array($type, self::__TYPES__, true))
                {
                        $this->_type = $type;
                        $methodGetDatas = "get{$type}Datas";
                        if (method_exists($this, $methodGetDatas))
                        {
                                $this->$methodGetDatas();
                        }
                }
                else
                {
                        $this->_type = false;
                        $this->_response->setError("504/$type");
                }
        }
        
        /*
        ** Accessor
        */        
        public function                             setError($error)
        {
                if ($this->_type)
                {
                        $this->_response->setError($error);
                }
        }
        
        public function                             getError()
        {
                return $this->_response->getError();
        }
        
        public function                             getType()
        {
                return $this->_type;
        }
        
        public function                             &getResponse()
        {
                return $this->_response;
        }   
        
        public function                             pushAnswer()
        {
                if ($this->_type)
                {
                        switch (($numargs = func_num_args()))
                        {
                                case 1:
                                        $this->_response->pushAnswer(func_get_arg(0));
                                break;
                                case 2:
                                        $this->_response->pushAnswer(func_get_arg(0), func_get_arg(1));
                                break;
                                default:
                                break;
                        }
                }
        }
        
        public function                             getAnswer()
        {
                return $this->_response->getAnswer();
        }
        
        public function                             getDatas()
        {
                if (!array_key_exists($this->_type, self::$_datasInit))
                {
                        $methodGetDatas = "get{$this->_type}Datas";
                        if (method_exists($this, $methodGetDatas))
                        {
                                self::$_datas[$this->_type] = (array_key_exists($this->_type, self::$_datas)) ? array_merge(self::$_datas[$this->_type], self::$methodGetDatas()) : self::$methodGetDatas();
                                self::$_datasInit[$this->_type] = true;
                        }
                }
                return self::$_datas[$this->_type];
        }
        
        /*
        ** ****** STATIC METHODS ****** **
        */
        
        /* Getters of datas by type */
        static private function                     getDatasArrayProtectHTML($arrayDatas)
        {
                $arrayDatasProtectHTML = array();
                foreach ($arrayDatas as $key => $value)
                {
                        $arrayDatasProtectHTML[htmlspecialchars($key)] = (is_array($value)) ? self::getDatasArrayProtectHTML($value) : htmlspecialchars($value);
                }
                return $arrayDatasProtectHTML;
        }
        
        static private function                     parseDatasFromInput()
        {
                $inputData = file_get_contents('php://input');
                $datas = array();
                parse_str($inputData, $datas);
                $datasProtect = array();
                foreach ($datas as $key => $value)
                {
                        $datasProtect[htmlspecialchars($key)] = (is_array($value)) ? self::getDatasArrayProtectHTML($value) : htmlspecialchars($value);
                }
                return $datasProtect;
        }
        
        static private function                     getDatasForType($type)
        {
                if ($type)
                $type = strtoupper($type);
                $methodGetDatas = "get{$type}Datas";
                if (method_exists($this, $methodGetDatas))
                {
                        self::$_datas[$this->_type] = self::$methodGetDatas();
                }
        }
        
        static private function                     getGETdatas()
        {
                $datas = array();
                foreach ($_GET as $key => $value)
                {
                        if ($key != 'request')
                        {
                                $datas[htmlspecialchars($key)] = (is_array($value)) ? self::getDatasArrayProtectHTML($value) : htmlspecialchars($value);
                        }
                }
                return $datas;
        }
        
        static private function                     getPOSTdatas()
        {
                $datas = array();
                foreach ($_POST as $key => $value)
                {
                        $datas[htmlspecialchars($key)] = (is_array($value)) ? self::getDatasArrayProtectHTML($value) : htmlspecialchars($value);
                }
                return $datas;
        }
        
        static private function                     getPUTdatas()
        {
                return self::parseDatasFromInput();
        }
        
        static private function                     getDELETEdatas()
        {
                return self::parseDatasFromInput();
        }
        
        /* Setters datas */
        static public function                      pushDatasOnTypeDatasArray($type, $datas, $overwrite = false)
        {
                if (is_string($type))
                {
                        $type = strtoupper($type);
                        if (in_array($type, self::__TYPES__) && is_array($datas))
                        {
                                foreach ($datas as $key => $value)
                                {
                                        if ($overwrite == true || (!array_key_exists($key, self::$_datas)))
                                        {
                                                self::$_datas[$type][$key] = $value;
                                        }
                                }
                        }
                }
        }
        
        /* API Keys */
        static public function                      loadKeyAPI($aKeyAPIByType)
        {
                if (empty(self::$_keys) && is_array($aKeyAPIByType))
                {
                        foreach ($aKeyAPIByType as $type => $keyAPI)
                        {
                                $type = strtoupper($type);
                                if (in_array($type, self::__TYPES__))
                                {
                                        self::$_keys[$type] = $keyAPI;
                                }
                        }
                }
                foreach (self::__TYPES__ as $type)
                {
                        if (!array_key_exists($type, self::$_keys))
                        {
                                self::$_keys[$type] = '';
                        }
                }
        }
        
        static public function                      keyIsValid($type, $keyAPI)
        {
                if (array_key_exists($type, self::$_keys))
                {
                        return (empty(self::$_keys[$type])) ? true : boolval($keyAPI == self::$_keys[$type]);
                }
                return false;
        }
}
