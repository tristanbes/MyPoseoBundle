<?php

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Api;

use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Doctrine\Common\Cache\Cache;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\Authentication\QueryParam;
use Http\Client\Common\Plugin\AuthenticationPlugin;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tristanbes\MyPoseoBundle\Connection\RestClient;
use Tristanbes\MyPoseoBundle\Exception\NotEnoughCreditsException;

/**
 * @see http://fr.myposeo.com/nos-api/api-search/
 */
class Search implements SearchInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param Client $client The http client
     * @param Cache  $cache  The Doctrine Cache interface
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
     * @return array
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
     * @param string  $keyword
     * @param string  $url
     * @param string  $searchEngine
     * @param string  $callback
     * @param integer $geolocId
     * @param integer $location
     * @param integer $maxPage
     *
     * @return array
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
        // @todo
    }

    public function getSemResult()
    {
        // @todo
    }
}
