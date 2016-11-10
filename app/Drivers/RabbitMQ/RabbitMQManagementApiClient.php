<?php

namespace Adminerng\Drivers\RabbitMQ;

use Adminerng\Core\Exception\ConnectException;

class RabbitMQManagementApiClient
{
    private $baseUrl;

    public function __construct($host = 'localhost', $port = '15672', $user = 'guest', $password = 'guest')
    {
        $this->baseUrl = 'http://' . $user . ':' . $password . '@' . $host . ':' . $port;
    }

    /**
     * @return just for check if user authorised
     */
    public function overview()
    {
        return $this->call('/api/overview');
    }

    /**
     * returns list of vhosts visible for user
     * @param string|null $user
     * @return array
     */
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

    /**
     * returns list of queues for vhost
     * @param string|null $vhost
     * @return array
     */
    public function getQueues($vhost = null)
    {
        $endpoint = '/api/queues';
        if ($vhost) {
            $endpoint .= '/' . urlencode($vhost);
        }
        $queues = $this->call($endpoint);
        return $queues;
    }

    public function getMessages($vhost, $queue)
    {
        $count = 0;
        foreach ($this->getQueues($vhost) as $vhostQueue) {
            if ($vhostQueue['name'] == $queue) {
                $count = $vhostQueue['messages'];
                break;
            }
        }
        if ($count == 0) {
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

    private function call($endpoint, $method = 'GET', $params = null)
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
