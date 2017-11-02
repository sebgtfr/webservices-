<?php

namespace                                           Request;

class                                               Client extends \Webservices\RequestController
{
        public function                         get(&$response)
        {
                if (array_key_exists('login', $_GET) && array_key_exists('password', $_GET))
                {
                        $dbPericles = $this->getDatabase('pericles');
                        $dbPericles->prepareQuery('SELECT c.id, c.nom, c.prenom, c.email, c.telephone, c.mobile, CONCAT(c.numero_adresse, c.libelle_adresse) AS adresse, c.code_postal FROM client AS c WHERE (c.email = :login OR c.telephone = :login OR c.mobile = :login) AND c.password = :password',
                                array('login' => htmlspecialchars($_GET['login']), 'password' => htmlspecialchars($_GET['password'])));
                        if (($request = $dbPericles->getQueryLine()))
                        {
                                $dbPericles->prepareQuery('SELECT d.reference, d.code_statut_dossier, d.code_statut_dossier_parent, d.libelle_statut_parent, d.libelle_statut_dossier, d.message, d.date_dernier_statut, t.libelle AS type_pret FROM dossier AS d LEFT OUTER JOIN type_pret AS t ON d.id_type_pret = t.id WHERE d.id_emprunteur = :clientId', array('clientId' => $request['id']));
                                $dossiers = $dbPericles->getAllQueryLine(false);
                                $response->pushAnswer($request);
                                if (is_array($dossiers) && !empty($dossiers))
                                {
                                        $response->pushAnswer('dossiers', $dossiers);
                                }
                        }
                        else
                        {
                                $response->setError("101");
                        }
                }
                else
                {
                        $response->setError("400/Client");
                }
        }
        
}