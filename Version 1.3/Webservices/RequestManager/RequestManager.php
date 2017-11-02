<?php

namespace                                           Webservices;

class                                               RequestManager
{
        private                                     $_requestName;
        private                                     $_paramsRequest;
        private                                     $_requests = array();
        
        public function                             __construct()
        {
                if (!empty(($request = rtrim((!empty($_GET['request'])) ? strip_tags(trim(htmlspecialchars($_GET['request']))) : '', '/'))))
                {
                        $argv = explode('/', $request);
                        if (array_key_exists('REQUEST_METHOD', $_SERVER))
                        {
                                $this->_requests[0] = new \Webservices\Request($_SERVER['REQUEST_METHOD']);
                        }
                        if (strtoupper(substr($argv[0], 0, 3)) === "GET")
                        {
                                $argv[0] = substr($argv[0], 3);
                                if (!array_key_exists(0, $this->_requests) || $this->_requests[0]->getType() != 'GET')
                                {
                                        $this->_requests[] = new \Webservices\Request('GET');
                                }
                        }
                        $requestNameParam = ucfirst($argv[0]);
                        $lenRequestNameParam = strlen($requestNameParam);
                        $this->_requestName = '';
                        $majNextChar = false;
                        for ($i = 0; $i < $lenRequestNameParam; ++$i)
                        {
                                if ($requestNameParam[$i] != '-' && $requestNameParam[$i] != '_' && $requestNameParam[$i] != ' ')
                                {
                                        $this->_requestName .= ($majNextChar) ? strtoupper($requestNameParam[$i]) : $requestNameParam[$i];
                                        $majNextChar = false;
                                }
                                else
                                {
                                        $majNextChar = true;
                                }
                        }
                        $this->_paramsRequest = array_slice($argv, 1);
                }
                else
                {
                        $this->_requestName = false;
                }
        }
        
        public function                             executeRequest()
        {
                if (!empty($this->_requestName))
                {
                        $nameRequestController = "\\Request\\" . $this->_requestName;
                        $requestController = new $nameRequestController($this->_paramsRequest);
                        $requestAnswer = array();
                        $accessGetSecondRequest = false;
                        foreach ($this->_requests as $request)
                        {
                                $datas = $request->getDatas();
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
                                                $request->setError('404/' . $this->_requestName . '::' . $request->getType());
                                        }
                                        $accessGetSecondRequest = true;
                                }
                                else
                                {
                                        $request->setError('1/' . $this->_requestName . '::' . $request->getType());
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
                        }
                        $requestAnswer['status'] = 'success';
                        return $requestAnswer;
                }
                return false;
        }
}
