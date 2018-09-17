<?php

namespace App\Helpers;


use Exception;
use GuzzleHttp\Client;

class DFApi
{

    protected $api_key;
    protected $base_url = 'https://api.api.ai/v1/';
    protected $ver = '20150910';


    /**
     * DialogFlow api helper constructor.
     */
    public function __construct()
    {

        $this->api_key = config('df.api_key');
    }


    public function query($phrase)
    {
        $phrase = trim($phrase);


        $data = $this->sendGetRequest('query', [
            'query' => $phrase,
            'lang' => 'ru',
            'sessionId' => uniqid(),

        ]);
        l::debug('apiai: ', $data);

        $speech = array_get($data, 'result.fulfillment.messages', []);


        $result = '';
        foreach ($speech as $sp) {
            $phrase = array_get($sp, 'speech', null);
            if ($phrase != null) {
                $result .= $phrase . "\n";
            }

        }

        l::debug('apiai: ', $result);

        return $result;
    }


    /**
     * @param string $method
     * @param array $options
     * @return mixed
     */
    public function sendGetRequest(string $method, array $options = [])
    {
        $options['v'] = $this->ver;

        try {
            /** @var Client $client */
            $client = new Client([
                'verify' => false,
            ]);

            $responseData = $client->request('GET', $this->base_url . $method,
                [
                    'headers' => ['Authorization' => 'Bearer ' . $this->api_key],
                    'query' => $options
                ]);

            return json_decode($responseData->getBody(), true);
        } catch (Exception $exception) {
            l::debug('-api_ai error: ', $exception->getMessage());
            return [];
        }

    }


}