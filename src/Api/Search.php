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
    private $client;

    public function __construct(RestClient $client)
    {
        $this->client = $client;
    }

    public function getSearchEngineExtensions(string $searchEngine, ?int $ttl = null): array
    {
        $cacheKey = sprintf('%s_locations', $searchEngine);

        $data = $this->client->get('tool/json', [
            'method'       => 'getLocations',
            'searchEngine' => $searchEngine,
        ], $cacheKey, $ttl);

        return $data;
    }

    public function getTownCode(string $name, string $country = 'FR'): array
    {
        $data = $this->client->get('tool/json', [
            'method'  => 'getGeoloc',
            'country' => $country,
            'city'    => $name,
        ]);

        return $data;
    }

    public function getUrlRankByKeyword(string $keyword, string $url, string $searchEngine = 'google', ?string $callbackUrl = null, ?int $geolocId = null, int $location = 13, ?int $maxPage = null): array
    {
        $options = [];

        if (null !== $callbackUrl) {
            $options['callback'] = $callbackUrl;
        }

        if (null !== $geolocId) {
            $options['geolocId'] = $geolocId;
        }

        if (null !== $maxPage) {
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
        throw new \RuntimeException(sprintf('Method "%s" is not implemented.', __METHOD__));
    }

    public function getSemResult()
    {
        throw new \RuntimeException(sprintf('Method "%s" is not implemented.', __METHOD__));
    }
}
