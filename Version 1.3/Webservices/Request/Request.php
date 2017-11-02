<?php

namespace                                           Webservices;

class                                               Request
{
        const                                       __TYPES__ = array('GET', 'POST', 'PUT', 'DELETE');
        static private                              $_keys = array();
    
        private                                     $_type;
        private                                     $_response;
        private                                     $_datas = array();
        
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
                return $this->_datas;
        }
        
        /* Getters of datas by type */
        public function                             getGETdatas()
        {
                foreach ($_GET as $key => $value)
                {
                        if ($key != 'request')
                        {
                                $this->_datas[htmlspecialchars($key)] = (is_array($value)) ? $value : htmlspecialchars($value);
                        }
                }
        }
        
        public function                             getPOSTdatas()
        {
                foreach ($_POST as $key => $value)
                {
                        $this->_datas[htmlspecialchars($key)] = (is_array($value)) ? $value : htmlspecialchars($value);
                }
        }
        
        public function                             getPUTdatas()
        {
                /* Les données PUT arrivent du flux */
                $putdata = file_get_contents('php://input');
                $aDatasLines = explode('&', $putdata);
                foreach ($aDatasLines as $datasLine)
                {
                        $aDatas = explode('=', $datasLine);
                        $this->_datas[htmlspecialchars($aDatas[0])] = array_key_exists(1, $aDatas) ?  htmlspecialchars($aDatas[1]) : '';
                }
        }
        
        public function                             getDELETEdatas()
        {
                /* Les données PUT arrivent du flux */
                $putdata = file_get_contents('php://input');
                $aDatasLines = explode('&', $putdata);
                foreach ($aDatasLines as $datasLine)
                {
                        $aDatas = explode('=', $datasLine);
                        $this->_datas[htmlspecialchars($aDatas[0])] = array_key_exists(1, $aDatas) ?  htmlspecialchars($aDatas[1]) : '';
                }
        }
        
        /* Static method */
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
