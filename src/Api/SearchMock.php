<?php

declare(strict_types=1);

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Api;

/**
 * Class used when the application is in test environment
 */
class SearchMock implements SearchInterface
{
    public function getSearchEngineExtensions(string $searchEngine, ?int $ttl = null): array
    {
        $data             = [];
        $data[13]['id']   = 13;
        $data[13]['name'] = '.fr (fr)';

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

    public function getTownCode(string $name, string $country = 'FR'): array
    {
        throw new \RuntimeException(sprintf('Method "%s" is not implemented.', __METHOD__));
    }

    public function getUrlRankByKeyword(string $keyword, string $url, string $searchEngine = 'google', ?string $callbackUrl = null, ?int $geolocId = null, int $location = 13, ?int $maxPage = null): array
    {
        throw new \RuntimeException(sprintf('Method "%s" is not implemented.', __METHOD__));
    }
}
