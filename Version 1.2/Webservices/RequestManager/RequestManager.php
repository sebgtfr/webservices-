<?php

namespace                                           Webservices;

class                                               RequestManager
{
        private                                     $_requestName;
        private                                     $__paramsRequest;
        private                                     $_requests = array();
        
        public function                             __construct()
        {
                if (!empty(($cmd = rtrim((!empty($_GET['cmd'])) ? strip_tags(trim(htmlspecialchars($_GET['cmd']))) : '', '/'))))
                {
                        $argv = explode('/', $cmd);
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
                        $this->_requestName = ucfirst($argv[0]);
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
                        foreach ($this->_requests as $request)
                        {
                                $type = strtolower($request->getType());
                                if (method_exists($requestController, $type))
                                {
                                        try
                                        {
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
