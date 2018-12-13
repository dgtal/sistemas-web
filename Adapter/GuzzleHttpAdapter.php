<?php

namespace DGtal\SistemasWeb\Adapter;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;

/**
 * Class GuzzleHttpAdapter
 */
class GuzzleHttpAdapter implements ConnectorInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     * @return mixed
     */
    public function connect(array $config)
    {
        $this->config = $this->getConfig($config);

        return $this->getAdapter();
    }

    /**
     * @param $config
     * @return array|null
     * @throws \InvalidArgumentException
     */
    private function getConfig($config)
    {
        return $config;
    }

    /**
     * @return Client
     */
    private function getAdapter()
    {
        return new Client(
            [
                'base_uri' => $this->config['apiurl'],
                'timeout' => 30,
                'query' => [
                    'user_id' => $this->config['user_id'],
                    'user_pass' => $this->config['user_pass'],
                ],
                'headers' => [
                    'User-Agent' => 'Sistemas Web API Interface',
                ]
            ]
        );
    }
}
