<?php
/**
 * Created by PhpStorm.
 * User: Vlad
 * Date: 09/09/2016
 * Time: 13:10
 */

namespace Api;


class ApiRequester
{

    private $Http_Headers;

    public function requeteTousContrats()
    {
        $UrlBuilder = new JCDecauxUrlBuilder(JCDecauxUrlBuilder::$CONTRAT);
        $url = $UrlBuilder->buildUrl();
//        var_dump($url);
        return json_decode($this->envoyerGet($url), true);

    }

    public function requeteToutesStations($contrat)
    {
        $UrlBuilder = new JCDecauxUrlBuilder(JCDecauxUrlBuilder::$STATIONS);
        if ($contrat) {
            $UrlBuilder->setParametresGet(['contract' => $contrat]);
        }
        $url = $UrlBuilder->buildUrl();
//        var_dump($url);
        return json_decode($this->envoyerGet($url), true);
    }

    public function requeteComplementStations($stations)
    {
        $str = file_get_contents('./Db/pvo_patrimoine_voirie.pvostationvelov_all.json-json.txt');
        $lines = explode(PHP_EOL, $str);

        $jsonData = file_get_contents($lines[1]);
        $Data = json_decode($jsonData, true)['values'];

        foreach ($stations as $key => $station) {
            foreach ($Data as $item) {
                if ($station['number'] == $item['idstation']) {

                    $tab = explode(' ', $item['commune']);
                    $station['city'] = $tab[0];
                    $station['arrondissement'] = sizeof($tab) > 1 ? $tab[1].' '.$tab[2] : '-';
                    $stations[$key]=  $station;
                }
            }
        }

        return $stations;
    }

    public function requeteStation($station_number, $contract_name)
    {
        $UrlBuilder = new JCDecauxUrlBuilder(JCDecauxUrlBuilder::$STATIONS);
        $UrlBuilder->setCible($UrlBuilder->getCible() . '/' . $station_number);
        if ($contract_name) {
            $UrlBuilder->setParametresGet(['contract' => $contract_name]);
        }
        $url = $UrlBuilder->buildUrl();
//        var_dump($url);
        return json_decode($this->envoyerGet($url), true);
    }

    public function envoyerGet($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $verbose = fopen('../results.txt', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURL_HTTP_VERSION_1_1, true);

        $result = curl_exec($ch);
        if ($result === FALSE) {
            printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
                htmlspecialchars(curl_error($ch)));
            return NULL;
        } else return $result;
    }

    /**
     * @return mixed
     */
    public function getHttpHeaders()
    {
        return $this->Http_Headers;
    }

    /**
     * @param mixed $Http_Headers
     */
    public function setHttpHeaders($Http_Headers)
    {
        $this->Http_Headers = $Http_Headers;
    }

}