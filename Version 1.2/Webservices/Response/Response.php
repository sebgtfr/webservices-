<?php

namespace                                           Webservices;

class                                               Response
{
        static private                              $_aErrorsFormat = array(0 => '');
        private                                     $_response;
        private                                     $_error;
        
        public function                             __construct()
        {
                $this->_response = array();
                $this->_error = '';
        }
        
        static public function                      loadErrorFormat()
        {
                switch (($numargs = func_num_args()))
                {
                        case 1:
                                if (is_array($aError = func_get_arg(0)) && !empty($aError))
                                {
                                        foreach ($aError as $codeError => $msgError)
                                        {
                                                if (is_int($codeError) || (is_string($codeError) && ctype_digit($codeError)))
                                                {
                                                        self::$_aErrorsFormat[intval($codeError)] = (is_string($msgError)) ? $msgError : '';
                                                }
                                        }
                                }
                        break;
                        case 2:
                                $codeError = func_get_arg(0);
                                $msgError = func_get_arg(1);
                                if (is_int($codeError) || (is_string($codeError) && ctype_digit($codeError)))
                                {
                                        self::$_aErrorsFormat[intval($codeError)] = (is_string($msgError)) ? $msgError : '';
                                }
                        break;
                        default:
                        break;
                }
        }
        
        public function                             setError($error)
        {
                if (is_string($error) && !empty($error) && empty($this->_error))
                {
                        $this->_error = $error;
                }
        }
        
        public function                             getError()
        {
                if (is_string($this->_error) && !empty($this->_error))
                {
                        $errorResponse = array('status' => 'error', 'codeError' => 0, 'msgError' => self::$_aErrorsFormat[0]);
                         if (!empty($aError = explode('/', $this->_error)) && ctype_digit($aError[0]))
                        {
                                $errorResponse['codeError'] = intval($aError[0]);
                                $msgErrorFormat = array_key_exists($errorResponse['codeError'], self::$_aErrorsFormat) ? self::$_aErrorsFormat[$errorResponse['codeError']] : '';
                                $lenMsgErrorFormat = strlen($msgErrorFormat);
                                $errorResponse['msgError'] = '';
                                for ($i = 0; $i < $lenMsgErrorFormat; ++$i)
                                {
                                        if ($msgErrorFormat[$i] == '$')
                                        {
                                                if (($i + 1) == $lenMsgErrorFormat || ($msgErrorFormat[$i + 1] == '$'))
                                                {
                                                        $errorResponse['msgError'] .= $msgErrorFormat[$i];
                                                        ++$i;
                                                }
                                                else
                                                {
                                                        $indexParam = 0;
                                                        $iTmp = ($i + 1);
                                                        for (; $iTmp < $lenMsgErrorFormat && ctype_digit($msgErrorFormat[$iTmp]); ++$iTmp)
                                                        {
                                                                $indexParam = $indexParam * 10 + intval($msgErrorFormat[$iTmp]);
                                                        }
                                                        if ($iTmp != ($i + 1))
                                                        {
                                                                if (array_key_exists($indexParam, $aError))
                                                                {
                                                                        $errorResponse['msgError'] .= $aError[$indexParam];
                                                                }
                                                                $i = $iTmp - 1;
                                                        }                                                        
                                                }
                                        }
                                        else
                                        {
                                                $errorResponse['msgError'] .= $msgErrorFormat[$i];
                                        }
                                }
                        }
                        return $errorResponse;
                }
                return false;
        }
                
        public function                             pushAnswer()
        {
                switch (($numargs = func_num_args()))
                {
                        case 1:
                                if (is_array($response = func_get_arg(0)) && !empty($response))
                                {
                                        $this->_response = array_merge($this->_response, $response);
                                }
                        break;
                        case 2:
                                $this->_response[func_get_arg(0)] = func_get_arg(1);
                        break;
                        default:
                        break;
                }
        }
        
        public function                             getAnswer()
        {
                if (!($response = $this->getError()))
                {
                        if (($response = (empty($this->_response)) ? false : $this->_response))
                        {
                                $response['status'] = 'success';
                        }
                }
                return $response;
        }
        
}
