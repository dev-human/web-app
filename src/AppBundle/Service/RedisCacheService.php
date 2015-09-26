<?php
/**
 * Service responsible for saving and retrieving cache data from Redis
 */

namespace AppBundle\Service;


class RedisCacheService
{
    protected $redis;

    static public $KEY_CACHE_TAGS        = 'devhuman_tags';
    static public $KEY_CACHE_COLLECTIONS = 'devhuman_collections';
    static public $KEY_CACHE_TOPSTORIES  = 'devhuman_topstories';

    public function __construct(RedisService $redis)
    {
        $this->redis = $redis;
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function set($key, $content)
    {
        $this->redis->set($key, $content);
    }

    public function save($key, $content)
    {
        $this->redis->set($key, serialize($content));
    }

    public function fetch($key)
    {
        return unserialize($this->redis->get($key));
    }

    public function saveJson($key, array $content)
    {
        $this->redis->set($key, json_encode($content));
    }

    public function getAsArray($key)
    {
        $content = $this->redis->get($key);

        return json_decode($content, 1);
    }
}
