<?php

namespace                                           Request;

class                                               Taux extends \Webservices\RequestController
{
        public function                             get(&$response)
        {
                $dbPecunia = $this->getDatabase('pecunia');
                $dbPecunia->prepareQuery('SELECT * FROM taux');
                $response->pushAnswer('taux', $dbPecunia->getAllQueryLine());
        }
}