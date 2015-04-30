<?php

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Api;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
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
     * @param Client $client The guzzle client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Process the API response, provides error handling
     *
     * @param Request $request
     *
     * @throws NotEnoughCreditsException
     * @throws \Exception
     *
     * @return array
     */
    public function processResponse(Request $request)
    {

        try {
            $response = $this->client->send($request);
        } catch (BadResponseException $e) {
            $json = $e->getResponse()->json();

            if ($json['myposeo']['code'] == '-1' && $json['myposeo']['message'] == 'No enough credits') {
                throw new NotEnoughCreditsException();
            }
        }

        $data = $response->json();

        if (isset($data['status']) && $data['status'] != "success") {
            throw new \Exception('MyPoseo API: ' . $data['message']);
        }

        return $data;
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
        $request = $this->client->createRequest('GET', 'tool/json');
        $query   = $request->getQuery();

        $query->set('method', 'getLocations');
        $query->set('searchEngine', $searchEngine);

        $data = $this->processResponse($request);

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
        $request = $this->client->createRequest('GET', 'tool/json');
        $query   = $request->getQuery();

        $query->set('method', 'getGeoloc');
        $query->set('country', $country);
        $query->set('city', $name);

        $data = $this->processResponse($request);

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
        $request = $this->client->createRequest('GET', 'tool/json');
        $query   = $request->getQuery();

        $query->set('method', 'getPosition');
        $query->set('keyword', $keyword);
        $query->set('url', $url);
        $query->set('searchEngine', $searchEngine);
        $query->set('location', $location);

        if ($callback) {
            $query->set('callback', $callback);
        }

        if ($geolocId) {
            $query->set('geolocId', $geolocId);
        }

        if ($maxPage) {
            $query->set('maxPage', $maxPage);
        }

        $data = $this->processResponse($request);

        return $data;
    }

    public function getNaturalSeoResult()
    {
    }

    public function getSemResult()
    {
    }
}
