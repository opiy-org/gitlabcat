<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 19.02.18
 * Time: 17:19
 */

namespace App\Helpers;


use Exception;
use GuzzleHttp\Client;

class Meow
{

    protected $api_key;
    protected $api_url = 'http://aws.random.cat/';

    /**
     * Meow constructor.
     * @param string $api_key
     */
    public function __construct(string $api_key = '')
    {
        $this->api_key = $api_key;
    }

    /**
     * Get random cat image link
     * @return array|mixed
     */
    public function getCat()
    {
        return $this->sendRequest('GET', 'meow');
    }

    /**
     * Send api request
     *
     * @param string $method
     * @param string $endPoint
     * @param array $data
     * @return array|mixed
     */
    protected function sendRequest(string $method = 'GET', string $endPoint, array $data = [])
    {
        try {
            $client = new Client([
                'verify' => false,
                'base_uri' => $this->api_url,
            ]);

            $response = $client->request($method, $endPoint,
                [
                    'query' => $data
                ]);

            return json_decode($response->getBody(), true);
        } catch (Exception $exception) {
            l::exc($this, $exception);
            return [];
        }
    }


}