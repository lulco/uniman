<?php

namespace Adminerng\Drivers\Rabbit;

use GuzzleHttp\Client;

class RabbitManagementApiClient
{
    private $guzzleClient;

    public function __construct($host = 'localhost', $port = '15672', $user = 'guest', $password = 'guest')
    {
        $baseUrl = 'http://' . $user . ':' . $password . '@' . $host . ':' . $port;
        $this->guzzleClient = new Client(['base_uri' => $baseUrl]);
    }

    public function getVhosts($user = null)
    {
        $response = $this->guzzleClient->get('/api/vhosts');
        $result = json_decode((string)$response->getBody(), true);
        $vhosts = [];
        foreach ($result as $item) {
            if ($user === null) {
                $vhosts[] = $item;
                continue;
            }
            $response = $this->guzzleClient->get('/api/vhosts/' . urlencode($item['name']) . '/permissions');
            $permissions = json_decode((string)$response->getBody(), true);
            foreach ($permissions as $permission) {
                if ($permission['user'] == $user) {
                    $vhosts[] = $item;
                    continue;
                }
            }
        }
        return $vhosts;
    }

    public function getQueues($vhost = null)
    {
        $endpoint = '/api/queues';
        if ($vhost) {
            $endpoint .= '/' . urlencode($vhost);
        }
        $response = $this->guzzleClient->get($endpoint);
        $queues = json_decode((string)$response->getBody(), true);
        return $queues;
    }
}
