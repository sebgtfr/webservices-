<?php

namespace                                           Request;

class                                               Taux extends \Webservices\RequestController
{
        public function                             get(&$response)
        {
                $dbPecunia = $this->getDatabase('pecunia');
                $dbPecunia->prepareQuery('SELECT * FROM taux');
                if (empty(($taux = $dbPecunia->getAllQueryLine())))
                {
                        $response->setError('101');
                }
                else
                {
                        $response->pushAnswer('taux', $taux);
                }
        }
}