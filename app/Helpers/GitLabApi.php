<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 19.02.18
 * Time: 17:19
 */

namespace App\Helpers;


use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;

class GitLabApi
{

    protected $api_key;
    protected $api_url;

    /**
     * GitLabApi constructor.
     * @param string $api_key
     */
    public function __construct(string $api_key)
    {
        $this->api_key = $api_key;
        $this->api_url = config('gitlab.api_url') . '/api/v4/';
    }


    /**
     * Get data about current user
     * @return array|mixed
     */
    public function getMe()
    {
        return $this->sendRequest('GET', 'user');
    }

    /**
     * Get current user issuzes list
     *
     * @return array|mixed
     */
    public function getMyIssues()
    {
        return $this->sendRequest('GET', 'issues', [
            'state' => 'opened',
            'scope' => 'assigned-to-me',
            'order_by' => 'updated_at'
        ]);
    }


    /**
     * Get project's open issues
     *
     * @param int $project_id
     * @param array $labels  - tags
     * @return array|mixed
     */
    public function getOpenIssues(int $project_id, array $labels = [])
    {
        $labels_txt = '';
        foreach ($labels as $label) {
            $labels_txt .= $label . ',';
        }

        $labels_txt = trim($labels_txt, ' ,');

        $retval = $this->sendRequest('GET', 'projects/' . $project_id . '/issues', [
            'state' => 'opened',
            'scope' => 'all',
            'labels' => $labels_txt,
            'order_by' => 'updated_at'

        ]);

        return $retval;
    }


    /**
     *  Get project open events
     *
     * @param int $project_id
     * @param Carbon $start
     * @param Carbon $end
     * @return array|mixed
     */
    public function getOpenedEvents(int $project_id, Carbon $start, Carbon $end)
    {
        return $this->sendRequest('GET', 'projects/' . $project_id . '/events', [
            'action' => 'created',
            'target_type' => 'issue',
            'after' => $start->format('Y-m-d'),
            'before' => $end->format('Y-m-d'),
        ]);
    }


    /**
     *  Get project closed events
     *
     * @param int $project_id
     * @param Carbon $start
     * @param Carbon $end
     * @return array|mixed
     */
    public function getClosedEvents(int $project_id, Carbon $start, Carbon $end)
    {
        return $this->sendRequest('GET', 'projects/' . $project_id . '/events', [
            'action' => 'closed',
            'target_type' => 'issue',
            'after' => $start->format('Y-m-d'),
            'before' => $end->format('Y-m-d'),
        ]);
    }


    /**
     *  Send request to gitlab api
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
                    'headers' => [
                        'Private-Token' => $this->api_key,
                    ],
                    'query' => $data
                ]);

            return json_decode($response->getBody(), true);
        } catch (Exception $exception) {
            l::exc($this, $exception);
            return [];
        }
    }


}