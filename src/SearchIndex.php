<?php

namespace Algolia\AlgoliaSearch;

use Algolia\AlgoliaSearch\Config\SearchConfig;
use Algolia\AlgoliaSearch\Exceptions\MissingObjectId;
use Algolia\AlgoliaSearch\Response\AbstractResponse;
use Algolia\AlgoliaSearch\Response\BatchIndexingResponse;
use Algolia\AlgoliaSearch\Response\IndexingResponse;
use Algolia\AlgoliaSearch\RequestOptions\RequestOptions;
use Algolia\AlgoliaSearch\Iterators\ObjectIterator;
use Algolia\AlgoliaSearch\Iterators\RuleIterator;
use Algolia\AlgoliaSearch\Iterators\SynonymIterator;
use Algolia\AlgoliaSearch\Response\MultiResponse;
use Algolia\AlgoliaSearch\Response\NullResponse;
use Algolia\AlgoliaSearch\RetryStrategy\ApiWrapperInterface;
use Algolia\AlgoliaSearch\Support\Helpers;

final class SearchIndex
{
    /**
     * @var string
     */
    private $indexName;

    /**
     * @var ApiWrapperInterface
     */
    private $api;

    /**
     * @var SearchConfig
     */
    private $config;

    /**
     * SearchIndex constructor.
     *
     * @param string              $indexName
     * @param ApiWrapperInterface $apiWrapper
     * @param SearchConfig        $config
     */
    public function __construct($indexName, ApiWrapperInterface $apiWrapper, SearchConfig $config)
    {
        $this->indexName = $indexName;
        $this->api = $apiWrapper;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->config->getAppId();
    }

    /**
     * @param mixed $query
     * @param array $requestOptions
     *
     * @return array
     */
    public function search($query, $requestOptions = array())
    {
        $query = (string) $query;

        if (is_array($requestOptions)) {
            $requestOptions['query'] = $query;
        } elseif ($requestOptions instanceof RequestOptions) {
            $requestOptions->addBodyParameter('query', $query);
        }

        return $this->api->read('POST', api_path('/1/indexes/%s/query', $this->indexName), $requestOptions);
    }

    /**
     * @deprecated Please use searchForFacetValues instead
     *
     * @param string $facetName
     * @param string $facetQuery
     * @param array  $requestOptions
     *
     * @return mixed
     */
    public function searchForFacetValue($facetName, $facetQuery, $requestOptions = array())
    {
        return $this->searchForFacetValues($facetName, $facetQuery, $requestOptions);
    }

    /**
     * @param string $facetName
     * @param string $facetQuery
     * @param array  $requestOptions
     *
     * @return mixed
     */
    public function searchForFacetValues($facetName, $facetQuery, $requestOptions = array())
    {
        if (is_array($requestOptions)) {
            $requestOptions['facetQuery'] = $facetQuery;
        } elseif ($requestOptions instanceof RequestOptions) {
            $requestOptions->addBodyParameter('facetQuery', $facetQuery);
        }

        return $this->api->read(
            'POST',
            api_path('/1/indexes/%s/facets/%s/query', $this->indexName, $facetName),
            $requestOptions
        );
    }

    /**
     * @param array $requestOptions
     *
     * @return mixed
     */
    public function getSettings($requestOptions = array())
    {
        if (is_array($requestOptions)) {
            $requestOptions['getVersion'] = 2;
        } elseif ($requestOptions instanceof RequestOptions) {
            $requestOptions->addQueryParameter('getVersion', 2);
        }

        return $this->api->read(
            'GET',
            api_path('/1/indexes/%s/settings', $this->indexName),
            $requestOptions
        );
    }

    /**
     * @param array $settings
     * @param array $requestOptions
     *
     * @return IndexingResponse
     */
    public function setSettings($settings, $requestOptions = array())
    {
        $default = array();
        if (is_bool($fwd = $this->config->getDefaultForwardToReplicas())) {
            $default['forwardToReplicas'] = $fwd;
        }

        $response = $this->api->write(
            'PUT',
            api_path('/1/indexes/%s/settings', $this->indexName),
            $settings,
            $requestOptions,
            $default
        );

        return new IndexingResponse($response, $this);
    }

    /**
     * @param string $objectId
     * @param array  $requestOptions
     *
     * @return mixed
     */
    public function getObject($objectId, $requestOptions = array())
    {
        return $this->api->read(
            'GET',
            api_path('/1/indexes/%s/%s', $this->indexName, $objectId),
            $requestOptions
        );
    }

    /**
     * @param array $objectIds
     * @param array $requestOptions
     *
     * @return mixed
     */
    public function getObjects($objectIds, $requestOptions = array())
    {
        if (is_array($requestOptions)) {
            $attributesToRetrieve = '';
            if (isset($requestOptions['attributesToRetrieve'])) {
                $attributesToRetrieve = $requestOptions['attributesToRetrieve'];
                unset($requestOptions['attributesToRetrieve']);
            }

            $request = array();
            foreach ($objectIds as $id) {
                $req = array(
                    'indexName' => $this->indexName,
                    'objectID' => (string) $id,
                );

                if ($attributesToRetrieve) {
                    $req['attributesToRetrieve'] = $attributesToRetrieve;
                }

                $request[] = $req;
            }

            $requestOptions['requests'] = $request;
        }

        return $this->api->read(
            'POST',
            api_path('/1/indexes/*/objects'),
            $requestOptions
        );
    }

    /**
     * @param array $object
     * @param array $requestOptions
     *
     * @return BatchIndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    public function saveObject($object, $requestOptions = array())
    {
        return $this->saveObjects(array($object), $requestOptions);
    }

    /**
     * @param array $objects
     * @param array $requestOptions
     *
     * @return AbstractResponse
     *
     * @throws MissingObjectId
     */
    public function saveObjects($objects, $requestOptions = array())
    {
        if (isset($requestOptions['autoGenerateObjectIDIfNotExist'])
            && $requestOptions['autoGenerateObjectIDIfNotExist']) {
            unset($requestOptions['autoGenerateObjectIDIfNotExist']);

            return $this->addObjects($objects, $requestOptions);
        }

        if (isset($requestOptions['objectIDKey']) && $requestOptions['objectIDKey']) {
            $objects = Helpers::mapObjectIDs($requestOptions['objectIDKey'], $objects);
            unset($requestOptions['objectIDKey']);
        }

        try {
            return $this->splitIntoBatches('updateObject', $objects, $requestOptions);
        } catch (MissingObjectId $e) {
            $message = "\nAll objects must have an unique objectID (like a primary key) to be valid.\n\n";
            $message .= "If your batch has a unique identifier but isn't called objectID,\n";
            $message .= "you can map it automatically using `saveObjects(\$objects, ['objectIDKey' => 'primary'])`\n\n";
            $message .= "Algolia is also able to generate objectIDs automatically but *it's not recommended*.\n";
            $message .= "To do it, use `saveObjects(\$objects, ['autoGenerateObjectIDIfNotExist' => true])`\n\n";

            throw new MissingObjectId($message, $e->getMessage());
        }
    }

    /**
     * @param array $objects
     * @param array $requestOptions
     *
     * @return BatchIndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    private function addObjects($objects, $requestOptions = array())
    {
        return $this->splitIntoBatches('addObject', $objects, $requestOptions);
    }

    /**
     * @param array $object
     * @param array $requestOptions
     *
     * @return BatchIndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    public function partialUpdateObject($object, $requestOptions = array())
    {
        return $this->partialUpdateObjects(array($object), $requestOptions);
    }

    /**
     * @param array $objects
     * @param array $requestOptions
     *
     * @return BatchIndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    public function partialUpdateObjects($objects, $requestOptions = array())
    {
        $action = 'partialUpdateObjectNoCreate';

        if (isset($requestOptions['createIfNotExists']) && $requestOptions['createIfNotExists']) {
            $action = 'partialUpdateObject';
            unset($requestOptions['createIfNotExists']);
        }

        return $this->splitIntoBatches($action, $objects, $requestOptions);
    }

    /**
     * @param array $objects
     * @param array $requestOptions
     *
     * @return MultiResponse
     */
    public function replaceAllObjects($objects, $requestOptions = array())
    {
        $safe = isset($requestOptions['safe']) && $requestOptions['safe'];
        unset($requestOptions['safe']);

        $tmpName = $this->indexName.'_tmp_'.uniqid('php_', true);
        $tmpIndex = new static($tmpName, $this->api, $this->config);

        // Copy all index resources from production index
        $copyResponse = $this->copyTo($tmpIndex->getIndexName(), array(
            'scope' => array('settings', 'synonyms', 'rules'),
        ));

        if ($safe) {
            $copyResponse->wait();
        }

        // Send records (batched automatically)
        $batchResponse = $tmpIndex->saveObjects($objects, $requestOptions);

        if ($safe) {
            $batchResponse->wait();
        }

        // Move temporary index to production
        $moveResponse = $this->moveFrom($tmpName);

        if ($safe) {
            $moveResponse->wait();
        }

        return new MultiResponse(array($copyResponse, $batchResponse, $moveResponse));
    }

    /**
     * @param string $objectId
     * @param array  $requestOptions
     *
     * @return BatchIndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    public function deleteObject($objectId, $requestOptions = array())
    {
        return $this->deleteObjects(array($objectId), $requestOptions);
    }

    /**
     * @param array $objectIds
     * @param array $requestOptions
     *
     * @return BatchIndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    public function deleteObjects($objectIds, $requestOptions = array())
    {
        $objects = array_map(function ($id) {
            return array('objectID' => $id);
        }, $objectIds);

        return $this->splitIntoBatches('deleteObject', $objects, $requestOptions);
    }

    /**
     * @param mixed $filters
     * @param array $requestOptions
     *
     * @return IndexingResponse
     */
    public function deleteBy($filters, $requestOptions = array())
    {
        $response = $this->api->write(
            'POST',
            api_path('/1/indexes/%s/deleteByQuery', $this->indexName),
            $filters,
            $requestOptions
        );

        return new IndexingResponse($response, $this);
    }

    /**
     * @param array $requestOptions
     *
     * @return IndexingResponse
     */
    public function clearObjects($requestOptions = array())
    {
        $response = $this->api->write(
            'POST',
            api_path('/1/indexes/%s/clear', $this->indexName),
            array(),
            $requestOptions
        );

        return new IndexingResponse($response, $this);
    }

    /**
     * @param array $requests
     * @param array $requestOptions
     *
     * @return IndexingResponse
     */
    public function batch($requests, $requestOptions = array())
    {
        $response = $this->rawBatch($requests, $requestOptions);

        return new IndexingResponse($response, $this);
    }

    /**
     * @param array $requests
     * @param array $requestOptions
     *
     * @return mixed
     */
    private function rawBatch($requests, $requestOptions = array())
    {
        return $this->api->write(
            'POST',
            api_path('/1/indexes/%s/batch', $this->indexName),
            array('requests' => $requests),
            $requestOptions
        );
    }

    /**
     * @param string $action
     * @param array  $objects
     * @param array  $requestOptions
     *
     * @return BatchIndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    private function splitIntoBatches($action, $objects, $requestOptions = array())
    {
        $allResponses = array();
        $batch = array();
        $batchSize = $this->config->getBatchSize();
        $count = 0;

        foreach ($objects as $object) {
            $batch[] = $object;
            $count++;

            if ($count === $batchSize) {
                if ('addObject' !== $action) {
                    Helpers::ensureObjectID($batch, 'All objects must have an unique objectID (like a primary key) to be valid.');
                }
                $allResponses[] = $this->rawBatch(Helpers::buildBatch($batch, $action), $requestOptions);
                $batch = array();
                $count = 0;
            }
        }

        if ('addObject' !== $action) {
            Helpers::ensureObjectID($batch, 'All objects must have an unique objectID (like a primary key) to be valid.');
        }

        // If not calls were made previously, not objects are passed
        // so we return a NullResponse
        // If there are already responses and something left in the
        // batch, we send it.
        if (empty($allResponses) && empty($batch)) {
            return new NullResponse();
        } elseif (!empty($batch)) {
            $allResponses[] = $this->rawBatch(Helpers::buildBatch($batch, $action), $requestOptions);
        }

        return new BatchIndexingResponse($allResponses, $this);
    }

    /**
     * @param array $requestOptions
     *
     * @return ObjectIterator
     */
    public function browseObjects($requestOptions = array())
    {
        return new ObjectIterator($this->indexName, $this->api, $requestOptions);
    }

    /**
     * @param string|int $query
     * @param array      $requestOptions
     *
     * @return mixed
     */
    public function searchSynonyms($query, $requestOptions = array())
    {
        $query = (string) $query;

        if (is_array($requestOptions)) {
            $requestOptions['query'] = $query;
        } elseif ($requestOptions instanceof RequestOptions) {
            $requestOptions->addBodyParameter('query', $query);
        }

        return $this->api->read(
            'POST',
            api_path('/1/indexes/%s/synonyms/search', $this->indexName),
            $requestOptions
        );
    }

    /**
     * @param string $objectId
     * @param array  $requestOptions
     *
     * @return mixed
     */
    public function getSynonym($objectId, $requestOptions = array())
    {
        return $this->api->read(
            'GET',
            api_path('/1/indexes/%s/synonyms/%s', $this->indexName, $objectId),
            $requestOptions
        );
    }

    /**
     * @param string $synonym
     * @param array  $requestOptions
     *
     * @return IndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    public function saveSynonym($synonym, $requestOptions = array())
    {
        return $this->saveSynonyms(array($synonym), $requestOptions);
    }

    /**
     * @param mixed $synonyms
     * @param array $requestOptions
     *
     * @return IndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    public function saveSynonyms($synonyms, $requestOptions = array())
    {
        $default = array();
        if (is_bool($fwd = $this->config->getDefaultForwardToReplicas())) {
            $default['forwardToReplicas'] = $fwd;
        }

        if ($synonyms instanceof \Iterator) {
            $iteratedOver = array();
            foreach ($synonyms as $r) {
                $iteratedOver[] = $r;
            }
            $synonyms = $iteratedOver;
        }

        if (empty($synonyms)) {
            return new NullResponse();
        }

        Helpers::ensureObjectID($synonyms, 'All synonyms must have an unique objectID to be valid');

        $response = $this->api->write(
            'POST',
            api_path('/1/indexes/%s/synonyms/batch', $this->indexName),
            $synonyms,
            $requestOptions,
            $default
        );

        return new IndexingResponse($response, $this);
    }

    /**
     * @param mixed $synonyms
     * @param array $requestOptions
     *
     * @return IndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    public function replaceAllSynonyms($synonyms, $requestOptions = array())
    {
        if (is_array($requestOptions)) {
            $requestOptions['replaceExistingSynonyms'] = true;
        } elseif ($requestOptions instanceof RequestOptions) {
            $requestOptions->addQueryParameter('replaceExistingSynonyms', true);
        }

        return $this->saveSynonyms($synonyms, $requestOptions);
    }

    /**
     * @param string $objectId
     * @param array  $requestOptions
     *
     * @return IndexingResponse
     */
    public function deleteSynonym($objectId, $requestOptions = array())
    {
        $default = array();
        if (is_bool($fwd = $this->config->getDefaultForwardToReplicas())) {
            $default['forwardToReplicas'] = $fwd;
        }

        $response = $this->api->write(
            'DELETE',
            api_path('/1/indexes/%s/synonyms/%s', $this->indexName, $objectId),
            array(),
            $requestOptions,
            $default
        );

        return new IndexingResponse($response, $this);
    }

    /**
     * @param array $requestOptions
     *
     * @return IndexingResponse
     */
    public function clearSynonyms($requestOptions = array())
    {
        $default = array();
        if (is_bool($fwd = $this->config->getDefaultForwardToReplicas())) {
            $default['forwardToReplicas'] = $fwd;
        }

        $response = $this->api->write(
            'POST',
            api_path('/1/indexes/%s/synonyms/clear', $this->indexName),
            array(),
            $requestOptions,
            $default
        );

        return new IndexingResponse($response, $this);
    }

    /**
     * @param array $requestOptions
     *
     * @return SynonymIterator
     */
    public function browseSynonyms($requestOptions = array())
    {
        return new SynonymIterator($this->indexName, $this->api, $requestOptions);
    }

    /**
     * @param string|int $query
     * @param array      $requestOptions
     *
     * @return mixed
     */
    public function searchRules($query, $requestOptions = array())
    {
        $query = (string) $query;

        if (is_array($requestOptions)) {
            $requestOptions['query'] = $query;
        } elseif ($requestOptions instanceof RequestOptions) {
            $requestOptions->addBodyParameter('query', $query);
        }

        return $this->api->read(
            'POST',
            api_path('/1/indexes/%s/rules/search', $this->indexName),
            $requestOptions
        );
    }

    /**
     * @param string $objectId
     * @param array  $requestOptions
     *
     * @return mixed
     */
    public function getRule($objectId, $requestOptions = array())
    {
        return $this->api->read(
            'GET',
            api_path('/1/indexes/%s/rules/%s', $this->indexName, $objectId),
            $requestOptions
        );
    }

    /**
     * @param string $rule
     * @param array  $requestOptions
     *
     * @return IndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    public function saveRule($rule, $requestOptions = array())
    {
        return $this->saveRules(array($rule), $requestOptions);
    }

    /**
     * @param mixed $rules
     * @param array $requestOptions
     *
     * @return IndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    public function saveRules($rules, $requestOptions = array())
    {
        $default = array();
        if (is_bool($fwd = $this->config->getDefaultForwardToReplicas())) {
            $default['forwardToReplicas'] = $fwd;
        }

        if ($rules instanceof \Iterator) {
            $iteratedOver = array();
            foreach ($rules as $r) {
                $iteratedOver[] = $r;
            }
            $rules = $iteratedOver;
        }

        if (empty($rules)) {
            return new NullResponse();
        }

        Helpers::ensureObjectID($rules, 'All rules must have an unique objectID to be valid');

        $response = $this->api->write(
            'POST',
            api_path('/1/indexes/%s/rules/batch', $this->indexName),
            $rules,
            $requestOptions,
            $default
        );

        return new IndexingResponse($response, $this);
    }

    /**
     * @param mixed $rules
     * @param array $requestOptions
     *
     * @return IndexingResponse|NullResponse
     *
     * @throws MissingObjectId
     */
    public function replaceAllRules($rules, $requestOptions = array())
    {
        if (is_array($requestOptions)) {
            $requestOptions['clearExistingRules'] = true;
        } elseif ($requestOptions instanceof RequestOptions) {
            $requestOptions->addQueryParameter('clearExistingRules', true);
        }

        return $this->saveRules($rules, $requestOptions);
    }

    /**
     * @param string $objectId
     * @param array  $requestOptions
     *
     * @return IndexingResponse
     */
    public function deleteRule($objectId, $requestOptions = array())
    {
        $default = array();
        if (is_bool($fwd = $this->config->getDefaultForwardToReplicas())) {
            $default['forwardToReplicas'] = $fwd;
        }

        $response = $this->api->write(
            'DELETE',
            api_path('/1/indexes/%s/rules/%s', $this->indexName, $objectId),
            array(),
            $requestOptions,
            $default
        );

        return new IndexingResponse($response, $this);
    }

    /**
     * @param array $requestOptions
     *
     * @return IndexingResponse
     */
    public function clearRules($requestOptions = array())
    {
        $default = array();
        if (is_bool($fwd = $this->config->getDefaultForwardToReplicas())) {
            $default['forwardToReplicas'] = $fwd;
        }

        $response = $this->api->write(
            'POST',
            api_path('/1/indexes/%s/rules/clear', $this->indexName),
            array(),
            $requestOptions,
            $default
        );

        return new IndexingResponse($response, $this);
    }

    /**
     * @param array $requestOptions
     *
     * @return RuleIterator
     */
    public function browseRules($requestOptions = array())
    {
        return new RuleIterator($this->indexName, $this->api, $requestOptions);
    }

    /**
     * @param int   $taskId
     * @param array $requestOptions
     *
     * @return mixed
     */
    public function getTask($taskId, $requestOptions = array())
    {
        if (!$taskId) {
            throw new \InvalidArgumentException('taskID cannot be empty');
        }

        return $this->api->read(
            'GET',
            api_path('/1/indexes/%s/task/%s', $this->indexName, $taskId),
            $requestOptions
        );
    }

    /**
     * @param int   $taskId
     * @param array $requestOptions
     *
     * @return void
     */
    public function waitTask($taskId, $requestOptions = array())
    {
        $retry = 1;
        $time = $this->config->getWaitTaskTimeBeforeRetry();

        do {
            $res = $this->getTask($taskId, $requestOptions);

            if ('published' === $res['status']) {
                return;
            }

            $retry++;
            usleep(($retry / 10) * $time); // 0.1 second
        } while (true);
    }

    /**
     * @param string     $method
     * @param string     $path
     * @param array      $requestOptions
     * @param array|null $hosts
     *
     * @return mixed
     */
    public function custom($method, $path, $requestOptions = array(), $hosts = null)
    {
        return $this->api->send($method, $path, $requestOptions, $hosts);
    }

    /**
     * @param array $requestOptions
     *
     * @return IndexingResponse
     */
    public function delete($requestOptions = array())
    {
        $response = $this->api->write(
            'DELETE',
            api_path('/1/indexes/%s', $this->indexName),
            array(),
            $requestOptions
        );

        return new IndexingResponse($response, $this);
    }

    /**
     * @param string $tmpIndexName
     * @param array  $requestOptions
     *
     * @return IndexingResponse
     */
    private function copyTo($tmpIndexName, $requestOptions = array())
    {
        $apiResponse = $this->api->write(
            'POST',
            api_path('/1/indexes/%s/operation', $this->indexName),
            array(
                'operation' => 'copy',
                'destination' => $tmpIndexName,
            ),
            $requestOptions
        );

        return new IndexingResponse($apiResponse, $this);
    }

    /**
     * @param string $tmpIndexName
     * @param array  $requestOptions
     *
     * @return IndexingResponse
     */
    private function moveFrom($tmpIndexName, $requestOptions = array())
    {
        $apiResponse = $this->api->write(
            'POST',
            api_path('/1/indexes/%s/operation', $tmpIndexName),
            array(
                'operation' => 'move',
                'destination' => $this->indexName,
            ),
            $requestOptions
        );

        return new IndexingResponse($apiResponse, $this);
    }
}
