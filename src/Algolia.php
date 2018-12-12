<?php

namespace Algolia\AlgoliaSearch;

use Algolia\AlgoliaSearch\Cache\NullCacheDriver;
use Algolia\AlgoliaSearch\Http\HttpClientInterface;
use Algolia\AlgoliaSearch\Log\DebugLogger;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

final class Algolia
{
    const VERSION = '2.0.1';

    /**
     * Holds an instance of the simple cache repository (PSR-16).
     *
     * @var \Psr\SimpleCache\CacheInterface|null
     */
    private static $cache;

    /**
     * Holds an instance of the logger (PSR-3).
     *
     * @var \Psr\Log\LoggerInterface|null
     */
    private static $logger;

    /**
     * @var \Algolia\AlgoliaSearch\Http\HttpClientInterface
     */
    private static $httpClient;

    public static function isCacheEnabled()
    {
        if (null === self::$cache) {
            return false;
        }

        return !self::getCache() instanceof NullCacheDriver;
    }

    /**
     * Gets the cache instance.
     *
     * @return \Psr\SimpleCache\CacheInterface
     */
    public static function getCache()
    {
        if (null === self::$cache) {
            self::setCache(new NullCacheDriver());
        }

        return self::$cache;
    }

    /**
     * Sets the cache instance.
     *
     * @param \Psr\SimpleCache\CacheInterface $cache
     */
    public static function setCache(CacheInterface $cache)
    {
        self::$cache = $cache;
    }

    /**
     * Gets the logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public static function getLogger()
    {
        if (null === self::$logger) {
            self::setLogger(new DebugLogger());
        }

        return self::$logger;
    }

    /**
     * Sets the logger instance.
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    public static function getHttpClient()
    {
        if (null === self::$httpClient) {
            if (class_exists('\GuzzleHttp\Client')) {
                self::setHttpClient(new \Algolia\AlgoliaSearch\Http\Guzzle6HttpClient());
            } else {
                self::setHttpClient(new \Algolia\AlgoliaSearch\Http\Php53HttpClient());
            }
        }

        return self::$httpClient;
    }

    public static function setHttpClient(HttpClientInterface $httpClient)
    {
        self::$httpClient = $httpClient;
    }

    public static function resetHttpClient()
    {
        self::$httpClient = null;
    }
}
