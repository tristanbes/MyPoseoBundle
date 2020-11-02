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
     *
     * @return list<array{ id: int, name: string }>
     */
    public function getSearchEngineExtensions(string $searchEngine, ?int $ttl = null): array;

    /**
     * Get the town's code
     *
     * @param string $name    The town name
     * @param string $country The country ISO
     *
     * @return list<array{ id: int, city_code: string, city_name: string, code_dep: string }>
     */
    public function getTownCode(string $name, string $country = 'FR'): array;

    /**
     * Retrieves the url position given a keyword
     *
     * @return list<array{ url_positioned: string, position: string, page: string, type: string, serp: string, nbr_results: int, top: string, keyword: string, url_search: string, searchEngine: string, location: int }>
     */
    public function getUrlRankByKeyword(string $keyword, string $url, string $searchEngine = 'google', ?string $callbackUrl = null, ?int $geolocId = null, int $location = 13, ?int $maxPage = null): array;

    /**
     * @return mixed
     */
    public function getNaturalSeoResult();

    /**
     * @return mixed
     */
    public function getSemResult();
}
