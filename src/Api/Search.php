<?php

declare(strict_types=1);

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Api;

use Tristanbes\MyPoseoBundle\Connection\RestClient;

/**
 * @see http://fr.myposeo.com/nos-api/api-search/
 */
class Search implements SearchInterface
{
    /**
     * @var RestClient
     */
    private $client;

    /**
     * @param RestClient $client The http client
     */
    public function __construct(RestClient $client)
    {
        $this->client = $client;
    }

    /**
     * Returns the identifiers of the search engine's extension
     *
     * @param string $searchEngine The search engine
     * @param int    $ttl          The time to live for the cache
     *
     * @return array [['id' => 1, 'name => '.fr'] [...]]
     */
    public function getSearchEngineExtensions($searchEngine, $ttl = null)
    {
        $cacheKey = sprintf('%s_locations', $searchEngine);

        $data = $this->client->get('tool/json', [
            'method'       => 'getLocations',
            'searchEngine' => $searchEngine,
        ], $cacheKey, $ttl);

        return $data;
    }

    /**
     * Get the town's code
     *
     * @param string $name    The town name
     * @param string $country The country ISO
     *
     * @return array [['id' => 1, 'city_code => '1234', 'city_name' => 'dunkerque', 'code_dep' : '59']]
     */
    public function getTownCode($name, $country = 'FR')
    {
        $data = $this->client->get('tool/json', [
            'method'  => 'getGeoloc',
            'country' => $country,
            'city'    => $name,
        ]);

        return $data;
    }

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
     *
     * @return array
     *
     *   {
     *     "url_positioned": "",
     *     "position": "+100",
     *     "page": "-",
     *     "type": "seo_natural",
     *     "serp": "",
     *     "nbr_results": 250000000,
     *     "top": "https://urltestdefault.com/path/to/image",
     *     "keyword": "keyword",
     *     "url_search": "lemonde.fr",
     *     "searchEngine": "google",
     *     "location": "13"
     *   }
     */
    public function getUrlRankByKeyword($keyword, $url, $searchEngine = 'google', $callback = null, $geolocId = null, $location = 13, $maxPage = null)
    {
        $options = [];

        if ($callback) {
            $options['callback'] = $callback;
        }

        if ($geolocId) {
            $options['geolocId'] = $geolocId;
        }

        if ($maxPage) {
            $options['maxPage'] = $maxPage;
        }

        $data = $this->client->get('tool/json', array_merge([
            'method'       => 'getPosition',
            'keyword'      => $keyword,
            'url'          => $url,
            'searchEngine' => $searchEngine,
            'location'     => $location,
        ], $options));

        return $data;
    }

    public function getNaturalSeoResult()
    {
        // @todo, feel free to send a PR
    }

    public function getSemResult()
    {
        // @todo, feel free to send a PR
    }
}
