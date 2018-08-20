<?php

namespace UniMan\Drivers\RabbitMQ;

use UniMan\Core\Exception\ConnectException;

class RabbitMQManagementApiClient
{
    private $baseUrl;

    public function __construct(string $host = 'localhost', string $port = '15672', string $user = 'guest', string $password = 'guest')
    {
        $this->baseUrl = 'http://' . $user . ':' . $password . '@' . $host . ':' . $port;
    }

    public function overview(): array
    {
        return $this->call('/api/overview');
    }

    public function getVhosts(?string $user = null): array
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
                if ($permission['user'] === $user) {
                    $vhosts[] = $item;
                    continue;
                }
            }
        }
        return $vhosts;
    }

    public function getQueues(?string $vhost = null): array
    {
        $endpoint = '/api/queues';
        if ($vhost) {
            $endpoint .= '/' . urlencode($vhost);
        }
        $queues = $this->call($endpoint);
        return $queues;
    }

    public function getMessages(string $vhost, string $queue): array
    {
        $count = 0;
        foreach ($this->getQueues($vhost) as $vhostQueue) {
            if ($vhostQueue['name'] === $queue) {
                $count = $vhostQueue['messages'];
                break;
            }
        }
        if ($count === 0) {
            return [];
        }

        $endpoint = '/api/queues/' . urlencode($vhost) . '/' . urlencode($queue) . '/get';
        $params = [
            'count' => $count,
            'requeue' => true,
            'encoding' => 'auto',
        ];
        $result = $this->call($endpoint, 'POST', json_encode($params));
        return $result;
    }

    private function call(string $endpoint, string $method = 'GET', ?array $params = null): array
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($params) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $response = curl_exec($ch);
        curl_close($ch);
        if (!$response) {
            return [];
        }
        $result = json_decode((string)$response, true);
        if (isset($result['error'])) {
            throw new ConnectException($result['reason']);
        }
        return $result;
    }
}
