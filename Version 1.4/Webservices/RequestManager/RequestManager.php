<?php

namespace                                           Webservices;

class                                               RequestManager
{
        private                                     $_requestCMD;
        private                                     $_requestsMethods;
        
        public function                             __construct()
        {
                if (empty(($this->_requestCMD = rtrim((!empty($_GET['request'])) ? strip_tags(trim(htmlspecialchars($_GET['request']))) : '', '/'))))
                {
                        $this->_requestCMD = false;
                }
                else
                {
                        $this->_requestsMethods = (array_key_exists('REQUEST_METHOD', $_SERVER)) ? array(htmlspecialchars($_SERVER['REQUEST_METHOD'])) : array();
                }
        }
        
        public function                             executeRequest()
        {
                return self::execute($this->_requestCMD, $this->_requestsMethods);
        }
        
        /*
        ** ****** STATIC METHODS ****** **
        */
        
        static private function                     getRequestNameFromRequestLibelle($requestLibelle)
        {
                $requestName = '';
                $requestNameParam = ucfirst($requestLibelle);
                $lenRequestNameParam = strlen($requestNameParam);
                $majNextChar = false;
                for ($i = 0; $i < $lenRequestNameParam; ++$i)
                {
                        if ($requestNameParam[$i] != '-' && $requestNameParam[$i] != '_' && $requestNameParam[$i] != ' ')
                        {
                                $requestName .= ($majNextChar) ? ucfirst($requestNameParam[$i]) : $requestNameParam[$i];
                                $majNextChar = false;
                        }
                        else
                        {
                                $majNextChar = true;
                        }
                }
                return $requestName;
        }
        
        static private function                     getArgsFromRequestCMD($requestCMD, &$requestName)
        {
                $argv = explode('/', $requestCMD);
                $requestName = \Webservices\RequestManager::getRequestNameFromRequestLibelle($argv[0]);
                return array_slice($argv, 1);
        }
        
        static public function                      execute($requestCMD, $requestsTypes = array())
        {
                if (is_string($requestCMD) && !empty($requestCMD) && is_array($requestsTypes))
                {
                        $requestAnswer = array();
                        $requestName = null;
                        $params = \Webservices\RequestManager::getArgsFromRequestCMD($requestCMD, $requestName);
                        if (strtoupper(substr($requestName, 0, 3)) === "GET")
                        {
                                $requestName = substr($requestName, 3);
                                if (!array_key_exists(0, $requestsTypes) || strtoupper($requestsTypes[0]) != 'GET')
                                {
                                        $requestsTypes[] = 'GET';
                                }
                        }
                        $nameRequestController = "\\Request\\$requestName";
                        $requestController = new $nameRequestController($params);
                        $accessGetSecondRequest = false;
                        foreach ($requestsTypes as $requestsType)
                        {
                                $request = new \Webservices\Request($requestsType);
                                $datas = $request->getDatas();
                                if ($request->getType() != false)
                                {
                                        $key = array_key_exists('keyAPI', $datas) ? $datas['keyAPI'] : '';
                                        if (($accessGetSecondRequest && $request->getType() == 'GET') || (\Webservices\Request::keyIsValid($request->getType(), $key)))
                                        {
                                                $type = strtolower($request->getType());
                                                if (method_exists($requestController, $type))
                                                {
                                                        try
                                                        {
                                                                $requestController->setDatas($datas);
                                                                $requestController->$type($request->getResponse());
                                                        }
                                                        catch (Exception $ex)
                                                        {
                                                                $request->setError($ex->getMessage());
                                                        }
                                                }
                                                else
                                                {
                                                        $request->setError('404/' . $requestName . '::' . $request->getType());
                                                }
                                                $accessGetSecondRequest = true;
                                        }
                                        else
                                        {
                                                $request->setError('1/' . $requestName . '::' . $request->getType());
                                        }
                                        if (($answer = $request->getAnswer()))
                                        {
                                                if ($request->getType())
                                                {
                                                        $requestAnswer[$request->getType()] = $answer;
                                                }
                                                else
                                                {
                                                        $requestAnswer['unknown'][] = $answer;
                                                }
                                        }
                                        else
                                        {
                                                $requestAnswer[$request->getType()] = array();
                                        }
                                        if (!array_key_exists('status', $requestAnswer[$request->getType()]))
                                        {
                                                $requestAnswer[$request->getType()]['status'] = 'success';
                                        }
                                }
                        }
                        $requestAnswer['status'] = 'success';
                        return $requestAnswer;
                }
                $errorResponse = new \Webservices\Response();
                $errorResponse->setError('500');
                return $errorResponse->getAnswer();
        }
}