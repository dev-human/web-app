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

    public function saveJsonCache($key, array $content)
    {
        $this->getClient()->set($key, json_encode($content));
    }

    public function getJsonCache($key, $decode = 1)
    {
        $content = $this->getClient()->get($key);

        if (!$content) {
            return null;
        }

        if ($decode) {
            $content = json_decode($content, 1);
        }

        return $content;
    }

    public function saveSerializedCache($key, $content)
    {
        $this->getClient()->set($key, serialize($content));
    }

    public function getSerializedCache($key)
    {
        return unserialize($this->getClient()->get($key));
    }

    public function getClient()
    {
        return $this->client;
    }
}
