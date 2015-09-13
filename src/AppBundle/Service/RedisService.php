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
}
