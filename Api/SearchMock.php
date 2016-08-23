<?php

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Api;

use Tristanbes\MyPoseoBundle\Connection\RestClient;

/**
 * Class used when the application is in test environment
 */
class SearchMock implements SearchInterface
{
    /**
     * @var RestClient
     */
    private $client;

    /**
     * @param RestClient $client The guzzle client
     */
    public function __construct(RestClient $client)
    {
        $this->client = $client;
    }

    /**
     * Returns the identifiers of the search engine's extension
     *
     * @param string  $searchEngine The search engine
     * @param integer $ttl          The time to live for the cache
     *
     * @return array
     */
    public function getSearchEngineExtensions($searchEngine, $ttl = null)
    {
        $data             = [];
        $data[13]['id']   = 13;
        $data[13]['name'] = '.fr (fr)';

        return $data;
    }

    public function getNaturalSeoResult()
    {
    }

    public function getSemResult()
    {
    }

    public function getTownCode($name, $country = 'FR')
    {
    }

    public function getUrlRankByKeyword($keyword, $url, $searchEngine = 'google', $callback = null, $geolocId = null, $location = 13, $maxPage = null)
    {
    }
}
