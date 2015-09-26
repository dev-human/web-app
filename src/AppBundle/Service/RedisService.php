<?php
/**
 * Provides the Redis client as a service
 */

namespace AppBundle\Service;

use Predis\Client;

class RedisService
{
    /** @var   */
    protected $client;

    public function __construct(array $parameters)
    {
        $this->client = new Client($parameters);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function set($key, $content)
    {
        $this->getClient()->set($key, $content);
    }

    public function get($key)
    {
        return $this->getClient()->get($key);
    }




}
