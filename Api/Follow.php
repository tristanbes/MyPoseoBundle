<?php

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Api;

use Guzzle\Http\Client;

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
     * List account sites
     *
     * @return array
     */
    public function getSites()
    {
        $request = $this->client->get('site',['debug' => true]);
        $response = $this->client->send($request);

        return $response;
    }

    /**
     * Add one or several sites
     *
     * @param array   $sites Array of urls you want to add
     * @param string  $label Label of the site
     * @param integer $group Group id
     *
     * @return array
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
     * Retrurns a site
     *
     * @param integer $id Site id
     *
     * @return array
     */
    public function getSite($id)
    {
        $request = $this->client->get(sprintf('site/%d', $id));
        $response = $this->client->send($request);

        return $response;
    }
}
