<?php

declare(strict_types=1);

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Api;

/**
 * @see http://fr.myposeo.com/nos-api/api-search/
 */
interface SearchInterface
{
    /**
     * Returns the identifiers of the search engine's extension
     *
     * @param string $searchEngine The search engine
     * @param int    $ttl          The time to live for the cache
     */
    public function getSearchEngineExtensions($searchEngine, $ttl = null);

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
     * @param string $keyword
     * @param string $url
     * @param string $searchEngine
     * @param string $callback
     * @param int    $geolocId
     * @param int    $location
     * @param int    $maxPage
     */
    public function getUrlRankByKeyword($keyword, $url, $searchEngine = 'google', $callback = null, $geolocId = null, $location = 13, $maxPage = null);

    public function getNaturalSeoResult();

    public function getSemResult();
}
