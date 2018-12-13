<?php

namespace DGtal\SistemasWeb;

use DGtal\SistemasWeb\Adapter\ConnectorInterface;
use GuzzleHttp\Exception\ClientException;

/**
 * Class SistemasWebManager
 */
class SistemasWebManager
{
    /**
     * @var
     */
    protected $config;

    /**
     * @var ConnectorInterface
     */
    protected $client;

    /**
     * SistemasWeb constructor.
     * @param array $config
     * @param ConnectorInterface $client
     */
    public function __construct(array $config, ConnectorInterface $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * @return ConnectorInterface
     */
    public function connection()
    {
        return $this->client->connect($this->getConfig());
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function execute(string $method, $parameters = [])
    {
        $parameters = array_merge_recursive($parameters, $this->connection()->getConfig());

        try {
            $response = $this->connection()->get($method, $parameters);
            $xml = simplexml_load_string($response->getBody()->getContents(), 'SimpleXMLElement', LIBXML_NOCDATA);

            $data = [
                'meta' => null,
                'posts' => [],
            ];

            foreach ($xml->children() as $node) {
                switch ($node->getName()) {
                    case 'meta':
                        $data[$node->getName()] = json_decode(json_encode($node), true);
                        break;
                    case 'post':
                        $data['posts'][] = json_decode(json_encode($node), true);
                        break;
                }
            }

            return $data;
        } catch (ClientException $ex) {
            return ['error' => $ex->getMessage()];
        }
    }

    /**
     * Get the config array.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Convert a SimpleXML object to an associative array
     *
     * @param object $xmlObject
     *
     * @return array
     * @access public
     */
    private function simpleXmlToArray($xmlObject)
    {
        $array = [];
        foreach ($xmlObject->children() as $node) {
            $array[$node->getName()] = is_array($node) ? $this->simpleXmlToArray($node) : (string) $node;
        }
        return $array;
    }
}
