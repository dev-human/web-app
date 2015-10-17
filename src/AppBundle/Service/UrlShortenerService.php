<?php
/**
 * Url Shortener Service using Google API
 */

namespace AppBundle\Service;

class UrlShortenerService
{
    static private $APIKEY = 'AIzaSyC3Q-0cdraqFwr-KrK90IVz-piBkudksfk';
    static private $API_ENDPOINT = 'https://www.googleapis.com/urlshortener/v1/url';

    public function shorten($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json; charset=utf-8' ]);
        curl_setopt($ch, CURLOPT_URL, self::$API_ENDPOINT . '?key=' . self::$APIKEY);
        curl_setopt($ch, CURLOPT_REFERER, "http://dev-human.io");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['longUrl' => $url]));

        $output = curl_exec($ch);

        curl_close($ch);

        return json_decode($output);
    }
}
