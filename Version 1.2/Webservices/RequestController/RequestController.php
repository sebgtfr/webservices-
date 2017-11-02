<?php

namespace                                           Webservices;

class                                               RequestController
{
        private                                     $_path;
        private                                     $_params;

        public function                             __construct($params)
        {
                $script = genPathClass(get_class($this));
                $this->_path = substr($script, 0, strlen($script) - strlen(strrchr($script, '/')));
                $this->_params = $params;
        }
        
        protected function                             getDatabase($idInstance)
        {
                return \Webservices\Database::getInstance($idInstance);
        }
        
        public function                             __call($name, $arguments = false)
        {
                if (substr($name, 0, 3) == 'get')
                {
                        $varname = '_' . lcfirst(substr($name, 3));
                        return (property_exists($this, $varname)) ? $this->$varname : false;
                }
                return false;
        }
}
