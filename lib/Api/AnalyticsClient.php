<?php

namespace Algolia\AlgoliaSearch\Api;

use Algolia\AlgoliaSearch\Algolia;
use Algolia\AlgoliaSearch\Configuration\AnalyticsConfig;
use Algolia\AlgoliaSearch\ObjectSerializer;
use Algolia\AlgoliaSearch\RetryStrategy\ApiWrapper;
use Algolia\AlgoliaSearch\RetryStrategy\ApiWrapperInterface;
use Algolia\AlgoliaSearch\RetryStrategy\ClusterHosts;

/**
 * AnalyticsClient Class Doc Comment
 *
 * @category Class
 * @package  Algolia\AlgoliaSearch
 */
class AnalyticsClient
{
    /**
     * @var ApiWrapperInterface
     */
    protected $api;

    /**
     * @var AnalyticsConfig
     */
    protected $config;

    /**
     * @param AnalyticsConfig $config
     * @param ApiWrapperInterface $apiWrapper
     */
    public function __construct(
        ApiWrapperInterface $apiWrapper,
        AnalyticsConfig $config
    ) {
        $this->config = $config;

        $this->api = $apiWrapper;
    }

    /**
     * Instantiate the client with basic credentials and region
     *
     * @param string $appId  Application ID
     * @param string $apiKey Algolia API Key
     * @param string $region Region
     */
    public static function create($appId = null, $apiKey = null, $region = null)
    {
        $allowedRegions = ['de', 'us'];
        $config = AnalyticsConfig::create(
            $appId,
            $apiKey,
            $region,
            $allowedRegions
        );

        return static::createWithConfig($config);
    }

    /**
     * Instantiate the client with configuration
     *
     * @param AnalyticsConfig $config Configuration
     */
    public static function createWithConfig(AnalyticsConfig $config)
    {
        $config = clone $config;

        if ($hosts = $config->getHosts()) {
            // If a list of hosts was passed, we ignore the cache
            $clusterHosts = ClusterHosts::create($hosts);
        } else {
            $clusterHosts = ClusterHosts::create(
                'analytics.' . $config->getRegion() . '.algolia.com'
            );
        }

        $apiWrapper = new ApiWrapper(
            Algolia::getHttpClient(),
            $config,
            $clusterHosts
        );

        return new static($apiWrapper, $config);
    }

    /**
     * @return AnalyticsConfig
     */
    public function getClientConfig()
    {
        return $this->config;
    }

    /**
     * Send requests to the Algolia REST API.
     *
     * @param string $path The path of the API endpoint to target, anything after the /1 needs to be specified. (required)
     * @param array $parameters Query parameters to be applied to the current query. (optional)
     *
     * @return array<string, mixed>|object
     */
    public function del($path, $parameters = null)
    {
        // verify the required parameter 'path' is set
        if ($path === null || (is_array($path) && count($path) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $path when calling del'
            );
        }

        $resourcePath = '/1{path}';
        $queryParams = [];
        $httpBody = [];

        if ($parameters !== null) {
            if ('form' === 'form' && is_array($parameters)) {
                foreach ($parameters as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams = $parameters;
            }
        }

        // path params
        if ($path !== null) {
            $resourcePath = str_replace('{path}', $path, $resourcePath);
        }

        return $this->sendRequest(
            'DELETE',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Send requests to the Algolia REST API.
     *
     * @param string $path The path of the API endpoint to target, anything after the /1 needs to be specified. (required)
     * @param array $parameters Query parameters to be applied to the current query. (optional)
     *
     * @return array<string, mixed>|object
     */
    public function get($path, $parameters = null)
    {
        // verify the required parameter 'path' is set
        if ($path === null || (is_array($path) && count($path) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $path when calling get'
            );
        }

        $resourcePath = '/1{path}';
        $queryParams = [];
        $httpBody = [];

        if ($parameters !== null) {
            if ('form' === 'form' && is_array($parameters)) {
                foreach ($parameters as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams = $parameters;
            }
        }

        // path params
        if ($path !== null) {
            $resourcePath = str_replace('{path}', $path, $resourcePath);
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get average click position.
     *
     * @param string $index The index name to target. (required)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetAverageClickPositionResponse
     */
    public function getAverageClickPosition(
        $index,
        $startDate = null,
        $endDate = null,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getAverageClickPosition'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getAverageClickPosition, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getAverageClickPosition, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/clicks/averageClickPosition';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get clicks per positions.
     *
     * @param string $index The index name to target. (required)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetClickPositionsResponse
     */
    public function getClickPositions(
        $index,
        $startDate = null,
        $endDate = null,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getClickPositions'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getClickPositions, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getClickPositions, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/clicks/positions';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get click-through rate (CTR).
     *
     * @param string $index The index name to target. (required)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetClickThroughRateResponse
     */
    public function getClickThroughRate(
        $index,
        $startDate = null,
        $endDate = null,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getClickThroughRate'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getClickThroughRate, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getClickThroughRate, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/clicks/clickThroughRate';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get conversion rate (CR).
     *
     * @param string $index The index name to target. (required)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetConversationRateResponse
     */
    public function getConversationRate(
        $index,
        $startDate = null,
        $endDate = null,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getConversationRate'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getConversationRate, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getConversationRate, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/conversions/conversionRate';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get no click rate.
     *
     * @param string $index The index name to target. (required)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetNoClickRateResponse
     */
    public function getNoClickRate(
        $index,
        $startDate = null,
        $endDate = null,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getNoClickRate'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getNoClickRate, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getNoClickRate, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/searches/noClickRate';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get no results rate.
     *
     * @param string $index The index name to target. (required)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetNoResultsRateResponse
     */
    public function getNoResultsRate(
        $index,
        $startDate = null,
        $endDate = null,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getNoResultsRate'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getNoResultsRate, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getNoResultsRate, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/searches/noResultRate';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get searches count.
     *
     * @param string $index The index name to target. (required)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetSearchesCountResponse
     */
    public function getSearchesCount(
        $index,
        $startDate = null,
        $endDate = null,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getSearchesCount'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getSearchesCount, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getSearchesCount, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/searches/count';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get top searches with no clicks.
     *
     * @param string $index The index name to target. (required)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param int $limit Number of records to return. Limit is the size of the page. (optional, default to 10)
     * @param int $offset Position of the starting record. Used for paging. 0 is the first record. (optional, default to 0)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetSearchesNoClicksResponse
     */
    public function getSearchesNoClicks(
        $index,
        $startDate = null,
        $endDate = null,
        $limit = 10,
        $offset = 0,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getSearchesNoClicks'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getSearchesNoClicks, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getSearchesNoClicks, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/searches/noClicks';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($limit !== null) {
            if ('form' === 'form' && is_array($limit)) {
                foreach ($limit as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['limit'] = $limit;
            }
        }

        if ($offset !== null) {
            if ('form' === 'form' && is_array($offset)) {
                foreach ($offset as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['offset'] = $offset;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get top searches with no results.
     *
     * @param string $index The index name to target. (required)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param int $limit Number of records to return. Limit is the size of the page. (optional, default to 10)
     * @param int $offset Position of the starting record. Used for paging. 0 is the first record. (optional, default to 0)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetSearchesNoResultsResponse
     */
    public function getSearchesNoResults(
        $index,
        $startDate = null,
        $endDate = null,
        $limit = 10,
        $offset = 0,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getSearchesNoResults'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getSearchesNoResults, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getSearchesNoResults, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/searches/noResults';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($limit !== null) {
            if ('form' === 'form' && is_array($limit)) {
                foreach ($limit as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['limit'] = $limit;
            }
        }

        if ($offset !== null) {
            if ('form' === 'form' && is_array($offset)) {
                foreach ($offset as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['offset'] = $offset;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get Analytics API status.
     *
     * @param string $index The index name to target. (required)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetStatusResponse
     */
    public function getStatus($index)
    {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getStatus'
            );
        }

        $resourcePath = '/2/status';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get top countries.
     *
     * @param string $index The index name to target. (required)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param int $limit Number of records to return. Limit is the size of the page. (optional, default to 10)
     * @param int $offset Position of the starting record. Used for paging. 0 is the first record. (optional, default to 0)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetTopCountriesResponse
     */
    public function getTopCountries(
        $index,
        $startDate = null,
        $endDate = null,
        $limit = 10,
        $offset = 0,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getTopCountries'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getTopCountries, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getTopCountries, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/countries';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($limit !== null) {
            if ('form' === 'form' && is_array($limit)) {
                foreach ($limit as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['limit'] = $limit;
            }
        }

        if ($offset !== null) {
            if ('form' === 'form' && is_array($offset)) {
                foreach ($offset as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['offset'] = $offset;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get top filter attributes.
     *
     * @param string $index The index name to target. (required)
     * @param string $search The query term to search for. Must match the exact user input. (optional)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param int $limit Number of records to return. Limit is the size of the page. (optional, default to 10)
     * @param int $offset Position of the starting record. Used for paging. 0 is the first record. (optional, default to 0)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetTopFilterAttributesResponse
     */
    public function getTopFilterAttributes(
        $index,
        $search = null,
        $startDate = null,
        $endDate = null,
        $limit = 10,
        $offset = 0,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getTopFilterAttributes'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getTopFilterAttributes, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getTopFilterAttributes, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/filters';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($search !== null) {
            if ('form' === 'form' && is_array($search)) {
                foreach ($search as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['search'] = $search;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($limit !== null) {
            if ('form' === 'form' && is_array($limit)) {
                foreach ($limit as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['limit'] = $limit;
            }
        }

        if ($offset !== null) {
            if ('form' === 'form' && is_array($offset)) {
                foreach ($offset as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['offset'] = $offset;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get top filters for the an attribute.
     *
     * @param string $attribute The exact name of the attribute. (required)
     * @param string $index The index name to target. (required)
     * @param string $search The query term to search for. Must match the exact user input. (optional)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param int $limit Number of records to return. Limit is the size of the page. (optional, default to 10)
     * @param int $offset Position of the starting record. Used for paging. 0 is the first record. (optional, default to 0)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetTopFilterForAttributeResponse
     */
    public function getTopFilterForAttribute(
        $attribute,
        $index,
        $search = null,
        $startDate = null,
        $endDate = null,
        $limit = 10,
        $offset = 0,
        $tags = null
    ) {
        // verify the required parameter 'attribute' is set
        if (
            $attribute === null ||
            (is_array($attribute) && count($attribute) === 0)
        ) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $attribute when calling getTopFilterForAttribute'
            );
        }
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getTopFilterForAttribute'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getTopFilterForAttribute, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getTopFilterForAttribute, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/filters/{attribute}';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($search !== null) {
            if ('form' === 'form' && is_array($search)) {
                foreach ($search as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['search'] = $search;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($limit !== null) {
            if ('form' === 'form' && is_array($limit)) {
                foreach ($limit as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['limit'] = $limit;
            }
        }

        if ($offset !== null) {
            if ('form' === 'form' && is_array($offset)) {
                foreach ($offset as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['offset'] = $offset;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        // path params
        if ($attribute !== null) {
            $resourcePath = str_replace(
                '{attribute}',
                ObjectSerializer::toPathValue($attribute),
                $resourcePath
            );
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get top filters for a no result search.
     *
     * @param string $index The index name to target. (required)
     * @param string $search The query term to search for. Must match the exact user input. (optional)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param int $limit Number of records to return. Limit is the size of the page. (optional, default to 10)
     * @param int $offset Position of the starting record. Used for paging. 0 is the first record. (optional, default to 0)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetTopFiltersNoResultsResponse
     */
    public function getTopFiltersNoResults(
        $index,
        $search = null,
        $startDate = null,
        $endDate = null,
        $limit = 10,
        $offset = 0,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getTopFiltersNoResults'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getTopFiltersNoResults, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getTopFiltersNoResults, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/filters/noResults';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($search !== null) {
            if ('form' === 'form' && is_array($search)) {
                foreach ($search as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['search'] = $search;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($limit !== null) {
            if ('form' === 'form' && is_array($limit)) {
                foreach ($limit as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['limit'] = $limit;
            }
        }

        if ($offset !== null) {
            if ('form' === 'form' && is_array($offset)) {
                foreach ($offset as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['offset'] = $offset;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get top hits.
     *
     * @param string $index The index name to target. (required)
     * @param string $search The query term to search for. Must match the exact user input. (optional)
     * @param bool $clickAnalytics Whether to include the click-through and conversion rates for a search. (optional, default to false)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param int $limit Number of records to return. Limit is the size of the page. (optional, default to 10)
     * @param int $offset Position of the starting record. Used for paging. 0 is the first record. (optional, default to 0)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetTopHitsResponse
     */
    public function getTopHits(
        $index,
        $search = null,
        $clickAnalytics = false,
        $startDate = null,
        $endDate = null,
        $limit = 10,
        $offset = 0,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getTopHits'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getTopHits, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getTopHits, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/hits';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($search !== null) {
            if ('form' === 'form' && is_array($search)) {
                foreach ($search as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['search'] = $search;
            }
        }

        if ($clickAnalytics !== null) {
            if ('form' === 'form' && is_array($clickAnalytics)) {
                foreach ($clickAnalytics as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['clickAnalytics'] = $clickAnalytics;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($limit !== null) {
            if ('form' === 'form' && is_array($limit)) {
                foreach ($limit as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['limit'] = $limit;
            }
        }

        if ($offset !== null) {
            if ('form' === 'form' && is_array($offset)) {
                foreach ($offset as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['offset'] = $offset;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get top searches.
     *
     * @param string $index The index name to target. (required)
     * @param bool $clickAnalytics Whether to include the click-through and conversion rates for a search. (optional, default to false)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param array $orderBy Reorder the results. (optional)
     * @param array $direction The sorting of the result. (optional)
     * @param int $limit Number of records to return. Limit is the size of the page. (optional, default to 10)
     * @param int $offset Position of the starting record. Used for paging. 0 is the first record. (optional, default to 0)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetTopSearchesResponse
     */
    public function getTopSearches(
        $index,
        $clickAnalytics = false,
        $startDate = null,
        $endDate = null,
        $orderBy = null,
        $direction = null,
        $limit = 10,
        $offset = 0,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getTopSearches'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getTopSearches, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getTopSearches, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/searches';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($clickAnalytics !== null) {
            if ('form' === 'form' && is_array($clickAnalytics)) {
                foreach ($clickAnalytics as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['clickAnalytics'] = $clickAnalytics;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($orderBy !== null) {
            if ('form' === 'form' && is_array($orderBy)) {
                foreach ($orderBy as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['orderBy'] = $orderBy;
            }
        }

        if ($direction !== null) {
            if ('form' === 'form' && is_array($direction)) {
                foreach ($direction as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['direction'] = $direction;
            }
        }

        if ($limit !== null) {
            if ('form' === 'form' && is_array($limit)) {
                foreach ($limit as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['limit'] = $limit;
            }
        }

        if ($offset !== null) {
            if ('form' === 'form' && is_array($offset)) {
                foreach ($offset as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['offset'] = $offset;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Get users count.
     *
     * @param string $index The index name to target. (required)
     * @param string $startDate The lower bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $endDate The upper bound timestamp (a date, a string like \&quot;2006-01-02\&quot;) of the period to analyze. (optional)
     * @param string $tags Filter metrics on the provided tags. Each tag must correspond to an analyticsTags set at search time. Multiple tags can be combined with the operators OR and AND. If a tag contains characters like spaces or parentheses, it should be URL encoded. (optional)
     *
     * @return array<string, mixed>|\Algolia\AlgoliaSearch\Model\Analytics\GetUsersCountResponse
     */
    public function getUsersCount(
        $index,
        $startDate = null,
        $endDate = null,
        $tags = null
    ) {
        // verify the required parameter 'index' is set
        if ($index === null || (is_array($index) && count($index) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $index when calling getUsersCount'
            );
        }
        if (
            $startDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $startDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "startDate" when calling AnalyticsClient.getUsersCount, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        if (
            $endDate !== null &&
            !preg_match(
                '/^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                $endDate
            )
        ) {
            throw new \InvalidArgumentException(
                'invalid value for "endDate" when calling AnalyticsClient.getUsersCount, must conform to the pattern /^\\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.'
            );
        }

        $resourcePath = '/2/users/count';
        $queryParams = [];
        $httpBody = [];

        if ($index !== null) {
            if ('form' === 'form' && is_array($index)) {
                foreach ($index as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['index'] = $index;
            }
        }

        if ($startDate !== null) {
            if ('form' === 'form' && is_array($startDate)) {
                foreach ($startDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['startDate'] = $startDate;
            }
        }

        if ($endDate !== null) {
            if ('form' === 'form' && is_array($endDate)) {
                foreach ($endDate as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['endDate'] = $endDate;
            }
        }

        if ($tags !== null) {
            if ('form' === 'form' && is_array($tags)) {
                foreach ($tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams['tags'] = $tags;
            }
        }

        return $this->sendRequest(
            'GET',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Send requests to the Algolia REST API.
     *
     * @param string $path The path of the API endpoint to target, anything after the /1 needs to be specified. (required)
     * @param array $parameters Query parameters to be applied to the current query. (optional)
     * @param array $body The parameters to send with the custom request. (optional)
     *
     * @return array<string, mixed>|object
     */
    public function post($path, $parameters = null, $body = null)
    {
        // verify the required parameter 'path' is set
        if ($path === null || (is_array($path) && count($path) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $path when calling post'
            );
        }

        $resourcePath = '/1{path}';
        $queryParams = [];
        $httpBody = [];

        if ($parameters !== null) {
            if ('form' === 'form' && is_array($parameters)) {
                foreach ($parameters as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams = $parameters;
            }
        }

        // path params
        if ($path !== null) {
            $resourcePath = str_replace('{path}', $path, $resourcePath);
        }

        if (isset($body)) {
            $httpBody = $body;
        }

        return $this->sendRequest(
            'POST',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    /**
     * Send requests to the Algolia REST API.
     *
     * @param string $path The path of the API endpoint to target, anything after the /1 needs to be specified. (required)
     * @param array $parameters Query parameters to be applied to the current query. (optional)
     * @param array $body The parameters to send with the custom request. (optional)
     *
     * @return array<string, mixed>|object
     */
    public function put($path, $parameters = null, $body = null)
    {
        // verify the required parameter 'path' is set
        if ($path === null || (is_array($path) && count($path) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $path when calling put'
            );
        }

        $resourcePath = '/1{path}';
        $queryParams = [];
        $httpBody = [];

        if ($parameters !== null) {
            if ('form' === 'form' && is_array($parameters)) {
                foreach ($parameters as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $queryParams = $parameters;
            }
        }

        // path params
        if ($path !== null) {
            $resourcePath = str_replace('{path}', $path, $resourcePath);
        }

        if (isset($body)) {
            $httpBody = $body;
        }

        return $this->sendRequest(
            'PUT',
            $resourcePath,
            $queryParams,
            $httpBody
        );
    }

    private function sendRequest(
        $method,
        $resourcePath,
        $queryParams,
        $httpBody
    ) {
        $query = \GuzzleHttp\Psr7\Query::build($queryParams);

        if ($method === 'GET') {
            $request = $this->api->read(
                $method,
                $resourcePath . ($query ? "?{$query}" : '')
            );
        } else {
            $request = $this->api->write(
                $method,
                $resourcePath . ($query ? "?{$query}" : ''),
                $httpBody
            );
        }

        return $request;
    }
}
