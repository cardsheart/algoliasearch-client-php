<?php

namespace Algolia\AlgoliaSearch\Config;

final class InsightsConfig extends AbstractConfig
{
    /**
     * @param string|null $appId
     * @param string|null $apiKey
     * @param string|null $region
     *
     * @return InsightsConfig
     */
    public static function create($appId = null, $apiKey = null, $region = null)
    {
        $config = array(
            'appId' => null !== $appId ? $appId : getenv('ALGOLIA_APP_ID'),
            'apiKey' => null !== $apiKey ? $apiKey : getenv('ALGOLIA_API_KEY'),
            'region' => null !== $region ? $region : 'us',
        );

        return new static($config);
    }

    /**
     * @param string $region
     *
     * @return $this
     */
    public function setRegion($region)
    {
        $this->config['region'] = $region;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->config['region'];
    }
}
