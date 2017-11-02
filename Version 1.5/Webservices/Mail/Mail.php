<?php

namespace                                           Webservices;

class                                               Mail
{
        /*
        ** ****** CONST ****** **
        */
        const                                       __NEWLINE__ = "\r\n";
        const                                       __DEFAULT_HEADER__ = array
        (
                'MIME-Version' => '1.0',
                'Content-type' => 'text/html',
                'charset' => 'UTF-8',
                'From' => '',
                'Reply-To' => '',
                'X-Mailer' => '',
                'Content-Transfer-Encoding' => '',
                'Date' => '',
                'To' => array(),
                'Cc' => array(),
                'Bcc' => array(),
        );
    
        /*
        ** ****** STATIC ATTRIBUTS ****** **
        */
        static private                              $_envIsDev = true;
        static private                              $_aToEnvDev = array();
        
        /*
        ** ****** ATTRIBUTS ****** **
        */
        private                                     $_to = array();
        private                                     $_subject = '';
        private                                     $_header;
        private                                     $_body = '';
        private                                     $_additionnalParameter = '';
        
        public function                             __construct()
        {
                $this->_header = self::__DEFAULT_HEADER__;
                $this->_header['X-Mailer'] = 'PHP/' . phpversion();
        }
        
        /*
        ** ****** METHODS ****** **
        */
        /* Généric function to push data on array */
        static private function                     push(&$aElem, $data)
        {
                if (is_string($data))
                {
                        if (!empty($data))
                        {
                                $aElem[] = $data;
                        }
                }
                else if (is_array($data))
                {
                        foreach ($data as $address)
                        {
                                if (is_string($address) && !empty($address))
                                {
                                        $aElem[] = $address;
                                }
                        }
                }
        }
        
        public function                             pushTo()
        {
                $argv = func_get_args();
                foreach ($argv as $to)
                {
                        self::push($this->_to, $to);
                }
                return $this;
        }
        
        public function                             pushHeader()
        {
                $argc = func_num_args();
                switch ($argc)
                {
                        case 1:
                                $aHeaderDatas = func_get_arg(0);
                                if (is_array($aHeaderDatas))
                                {
                                        foreach ($aHeaderDatas as $headerKey => $headerValue)
                                        {
                                                $this->pushIntoHeaderArray($headerKey, $headerValue);
                                        }
                                }
                        break;
                        case 2:
                                $aHeaderDatas = func_get_args();
                                $this->pushIntoHeaderArray($aHeaderDatas[0], $aHeaderDatas[1]);
                        break;
                        default:
                        break;
                }
                return $this;
        }
        
        public function                            pushIntoHeaderArray($headerKey, $headerValue)
        {
                if (is_string($headerKey) && !empty($headerKey))
                {
                        if (!array_key_exists($headerKey, $this->_header) || (is_string($headerValue) && is_string($this->_header[$headerKey])))
                        {
                                $this->_header[$headerKey] = $headerValue;
                        }
                        else if (is_array($this->_header[$headerKey]))
                        {
                                self::push($this->_header[$headerKey], $headerValue);
                        }
                }
                return $this;
        }
        
        public function                             setSubject($subject)
        {
                if (is_string($subject) && !empty($subject))
                {
                        $this->_subject = $subject;
                }
                return $this;
        }
        
        public function                             setBody($body)
        {
                if (is_string($body) && !empty($body))
                {
                        $this->_body = $body;
                }
                return $this;
        }
        
        public function                             setAdditionnalParameter($additionnalParameter)
        {
                if (is_string($additionnalParameter) && !empty($additionnalParameter))
                {
                        $this->_additionnalParameter = $additionnalParameter;
                }
                return $this;
        }
        
        public function                             send()
        {
                $to = '';
                $headers = '';
                $refArrayTo = (self::$_envIsDev) ? self::$_aToEnvDev : $this->_to;
                foreach ($refArrayTo as $aTo)
                {
                        if (!empty($to))
                        {
                                $to .= ', ';
                        }
                        $to .= $aTo;
                }
                if (!empty($to))
                {
                        $prodDeleteHeader = array('To', 'Cc', 'Bcc');
                        $contentTypeHeader = '';
                        $charsetHeader = '';
                        foreach ($this->_header as $headerKey => $headerValue)
                        {
                                if (!self::$_envIsDev || !in_array($headerKey, $prodDeleteHeader))
                                {
                                        switch ($headerKey)
                                        {
                                                case 'Content-type':
                                                        $contentTypeHeader = "Content-type: $headerValue";
                                                break;
                                                case 'charset':
                                                        $charsetHeader = "charset=$headerValue";
                                                break;
                                                default:
                                                        if (is_string($headerValue))
                                                        {
                                                                if (!empty($headerValue))
                                                                {
                                                                        $headers .= "$headerKey: $headerValue" . self::__NEWLINE__;
                                                                }
                                                        }
                                                        else
                                                        {
                                                                $contentHeader = '';
                                                                foreach ($headerValue as $value)
                                                                {
                                                                        if (!empty($contentHeader))
                                                                        {
                                                                                $contentHeader .= ', ';
                                                                        }
                                                                        $contentHeader .= $value;
                                                                }
                                                                if (!empty($contentHeader))
                                                                {
                                                                        $headers .= "$headerKey: $contentHeader" . self::__NEWLINE__;
                                                                }
                                                        }
                                                break;
                                        }
                                }
                        }
                        if (!empty($contentTypeHeader))
                        {
                                $headers .= "$contentTypeHeader;";
                                if (!empty($charsetHeader))
                                {
                                        $headers .= " $charsetHeader";
                                }
                                $headers .= self::__NEWLINE__;
                        }
                        return @mail($to, $this->_subject, $this->_body, $headers, $this->_additionnalParameter);
                }
                return false;
        }
        
        /*
        ** ****** STATIC METHODS ****** **
        */
        static public function                      setEnv($env)
        {
                if (is_bool($env))
                {
                        self::$_envIsProd = $env;
                }
                else if (is_string($env))
                {
                        if ($env == 'prod')
                        {
                                self::$_envIsDev = false;
                        }
                        else if ($env == 'dev')
                        {
                                self::$_envIsDev = true;
                        }
                }
        }
        
        static public function                      pushToInDevArray()
        {
                $argv = func_get_args();
                foreach ($argv as $to)
                {
                        \Webservices\Mail::push(\Webservices\Mail::$_aToEnvDev, $to);
                }
        }
}
