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

        return (isset($data['data']) ? $data['data'] : $data['msg']);
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
        $data     = $this->processResponse($response);

        return $data;
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
        $data     = $this->processResponse($response);

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
        $data     = $this->processResponse($response);

        return $data;
    }

    /**
     * Add keywords
     *
     * @param int   $id           The site's id
     * @param int   $keywordGroup ID keywords group
     * @param array $keywords     Array of keywords
     * @param int   $searchEngine ID search engine
     * @param int   $idLocation   ID of the search engine location. Id of country listed there :
     *                            http://api.myposeo.com/m/apiv2/tool/json?key=YOUR_API_KEY&method=getLocations&searchEngine=google
     * @param int   $idDevice     Keywords list(0 = desktop, 1 = Mobile)
     *
     * @return array
     */
    public function addKeywords($id, $keywordGroup, $keywords, $searchEngine = 2, $idLocation = 13, $idDevice = 0)
    {
        $request = $this->client->createRequest('POST', 'keyword');

        $request->addPostFields([
            'site'                           => $id,
            'keywords[]'                     => $keywords,
            'keywordsGroup'                  => $keywordGroup,
            'keywordsSetups[0][id_engine]'   => $searchEngine,
            'keywordsSetups[0][id_device]'   => $idDevice,
            'keywordsSetups[0][id_location]' => $idLocation
        ]);

        $response = $this->client->send($request);
        $data     = $this->processResponse($response);

        return $data;
    }

    /**
     * Returns rank for site'keywords
     *
     * @param int    $id           The site's ID
     * @param string $date         The date (format YYYY-MM-DD)
     * @param string $dateCompare  The compared date
     * @param stirng $type         Gives the position of competitors if set to "competitors"
     *
     * @return array
     */
    public function getRank($id, $date = null, $dateCompare = null, $type = null)
    {
        $request = $this->client->createRequest('GET', 'ranking');
        $query   = $request->getQuery();

        $query->set('site', $id);

        if ($date) {
            $query->set('date', $date);
        }

        if ($dateCompare) {
            $query->set('dateCompare', $dateCompare);
        }

        if ($type) {
            $query->set('type', $type);
        }

        $response = $this->client->send($request);
        $data     = $this->processResponse($response);

        return $data;
    }
}
