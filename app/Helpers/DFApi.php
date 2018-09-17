<?php

namespace App\Helpers;


use DialogFlow\Client;
use Exception;

class DFApi
{

    protected $api_key;

    /**
     * DialogFlow api helper constructor.
     */
    public function __construct()
    {

        $this->api_key = config('df.api_key');
    }


    /**
     *  Send request
     */
    public function sendRequest(string $query)
    {
        try {
            $client = new Client($this->api_key);

            $response_data = $client->get('query', [
                'query' => $query,
            ]);

            $response = json_decode((string)$response_data->getBody(), true);

        } catch (Exception $exception) {
            l::exc($this, $exception);
            $response = [];
        }

        return $response;
    }


}