<?php

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Api;

use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * @see http://fr.myposeo.com/nos-api/api-search/
 */
interface SearchInterface
{
    /**
     * Process the API request
     *
     * @param Request $request The guzzle request
     * @param string  $cacheName
     * @param integer $ttl
     *
     * @return
     */
    public function doRequest(Request $request, $cacheName = null, $ttl = null);

    /**
     * Process the API response, provides error handling
     *
     * @param Response $response The guzzle response
     */
    public function processResponse(Response $response);

    /**
     * Returns the identifiers of the search engine's extension
     *
     * @param string $searchEngine The search engine
     */
    public function getSearchEngineExtensions($searchEngine);

    /**
     * Get the town's code
     *
     * @param string $name    The town name
     * @param string $country The country ISO
     */
    public function getTownCode($name, $country = 'FR');

    /**
     * Retrieves the url position given a keyword
     *
     * @param string  $keyword
     * @param string  $url
     * @param string  $searchEngine
     * @param string  $callback
     * @param integer $geolocId
     * @param integer $location
     * @param integer $maxPage
     */
    public function getUrlRankByKeyword($keyword, $url, $searchEngine = 'google', $callback = null, $geolocId = null, $location = 13, $maxPage = null);

    public function getNaturalSeoResult();

    public function getSemResult();
}
