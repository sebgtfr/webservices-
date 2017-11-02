<?php

namespace                                           Webservices;

class                                               Request
{
        const                                       __TYPES__ = array('GET', 'POST', 'PUT', 'DELETE');
    
        private                                     $_type;
        private                                     $_response;
        
        public function                             __construct($type)
        {
                $this->_response = new \Webservices\Response();
                if (is_string($type) && ($type = strtoupper($type)) && in_array($type, self::__TYPES__, true))
                {
                        $this->_type = $type;
                }
                else
                {
                        $this->_type = false;
                        $this->_response->setError("504/$type");
                }
        }
        
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
        
}
