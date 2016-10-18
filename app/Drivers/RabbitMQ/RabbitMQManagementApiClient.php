<?php

namespace Adminerng\Drivers\RabbitMQ;

class RabbitMQManagementApiClient
{
    private $baseUrl;

    public function __construct($host = 'localhost', $port = '15672', $user = 'guest', $password = 'guest')
    {
        $this->baseUrl = 'http://' . $user . ':' . $password . '@' . $host . ':' . $port;
    }

    public function getVhosts($user = null)
    {
        $result = $this->call('/api/vhosts');
        $vhosts = [];
        foreach ($result as $item) {
            if ($user === null) {
                $vhosts[] = $item;
                continue;
            }
            $permissions = $this->call('/api/vhosts/' . urlencode($item['name']) . '/permissions');
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
        $queues = $this->call($endpoint);
        return $queues;
    }

    private function call($endpoint)
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $response = curl_exec($ch);
        if (!$response) {
            return [];
        }
        curl_close($ch);
        return json_decode((string)$response, true);
    }
}
