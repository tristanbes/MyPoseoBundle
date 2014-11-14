<?php

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Api;

use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;

/**
 * @see http://fr.myposeo.com/nos-api/api-search/
 */
class Search
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
     * @param Response $response The guzzle response
     *
     * @throws \LogicException
     *
     * @return array
     */
    public function processResponse(Response $response)
    {
        $data = $response->json();

        if ($data['state'] != 1) {
            return new \LogicException($data['msg']);
        }

        return (isset($data['data']) ? $data['data'] : $data['msg']);
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
        $request  = $this->client->createRequest('GET', '/tool/json');

        $query   = $request->getQuery();
        $body    = $request->getBody();

        $query->set('method', 'getLocations');
        $query->set('searchEngine', $searchEngine);

        $response = $this->client->send($request);
        $data     = $this->processResponse($response);

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
        $request  = $this->client->createRequest('GET', '/tool/json');

        $query   = $request->getQuery();
        $body    = $request->getBody();

        $query->set('method', 'getGeoloc');
        $query->set('country', $country);
        $query->set('city', $name);

        $response = $this->client->send($request);
        $data     = $this->processResponse($response);

        return $data;
    }

    public function getUrlRankByKeyword()
    {
    }

    public function getNaturalSeoResult()
    {
    }

    public function getSemResult()
    {
    }
}
