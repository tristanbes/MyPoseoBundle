<?php

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Api;

use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;

/**
 * Class used when the application is in test environment
 */
class SearchMock implements SearchInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client The guzzle client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Returns the identifiers of the search engine's extension
     *
     * @param string $searchEngine The search engine
     *
     * @return array
     */
    public function getSearchEngineExtensions($searchEngine)
    {
        $data             = [];
        $data[13]['id']   = 13;
        $data[13]['name'] = '.fr (fr)';

        return $data;
    }

    public function processResponse(Request $request)
    {
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
