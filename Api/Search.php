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


}
