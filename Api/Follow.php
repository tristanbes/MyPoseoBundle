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
 * SEO Follow given keywords
 */
class Follow
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

        return $data['data'];
    }

    /**
     * List account sites
     *
     * @return array
     */
    public function getSites()
    {
        $request  = $this->client->createRequest('GET', 'site');
        $response = $this->client->send($request);

        $data = $this->processResponse($response);

        return $data;
    }

    /**
     * Get a site
     *
     * @param int $id The site's id
     *
     * @return array
     */
    public function getSite($id)
    {
        $request  = $this->client->createRequest('GET', 'site/'.$id);
        $response = $this->client->send($request);

        $data = $this->processResponse($response);

        return $data;
    }

    /**
     * Add one or several sites
     *
     * @param array  $sites Array of urls you want to add
     * @param string $label Label of the site
     * @param id     $group Group id
     */
    public function addSites(array $sites, $label, $group)
    {
        $sites = (!is_array($sites)) ?: implode(',', $sites);

        $request = $this->client->createRequest('POST', 'site');
        $query   = $request->getQuery();
        $body    = $request->getBody();

        $query->set('label', $label);
        $query->set('group', $group);
        $body->set('sites', $sites);

        $response = $this->client->send($request);

        return $response->json();
    }

    /**
     * Update a site's comment
     *
     * @param int    $id      The site's id
     * @param string $comment The comment
     *
     * @return array
     */
    public function updateSite($id, $comment)
    {
        $request = $this->client->createRequest('PUT', 'site/'.$id);
        $query   = $request->getQuery();

        $query->set('comment', $id);

        $response = $this->client->send($request);

        $data = $this->processResponse($response);

        return $data;
    }

    /**
     * Deletes a site
     *
     * @param int $id The site's id
     *
     * @return array
     */
    public function deleteSite($id)
    {
        $request  = $this->client->createRequest('DELETE', 'site/'.$id);
        $response = $this->client->send($request);

        $data = $this->processResponse($response);

        return $data;
    }

    /**
     * List keywords attached to a site
     *
     * @param int $id The site's id
     *
     * @return array
     */
    public function getKeyword($id)
    {
        $request = $this->client->createRequest('GET', 'keyword');
        $query   = $request->getQuery();

        $query->set('keyword', $id);

        $response = $this->client->send($request);

        $data = $this->processResponse($response);

        return $data;
    }

    /**
     * Add keywords
     *
     * @param int   $id           The site's id
     * @param int   $keywordGroup ID keywords group
     * @param int   $searchEngine ID search engine
     * @param int   $idLocation   ID device. (0 => desktop, 1 => Mobile)
     * @param array $idDevice     Keywords list
     *
     * @return array
     */
    public function addKeywords($id, $keywordGroup, $searchEngine = 2, $idLocation = 13, $idDevice = 0)
    {
        $request = $this->client->createRequest('POST', 'keyword');
        $body    = $request->getBody();

        $body->set('site', $id);
        $body->set('keywordsGroup', $keywordGroup);
        $body->set('keywordsSetups[0][id_engine]', $searchEngine);
        $body->set('sites', $sites);

        $response = $this->client->send($request);

        $data = $this->processResponse($response);

        return $data;
    }
}
