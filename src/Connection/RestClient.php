<?php

declare(strict_types=1);

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Connection;

use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\Authentication\QueryParam;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Tristanbes\MyPoseoBundle\Exception\NotEnoughCreditsException;
use Tristanbes\MyPoseoBundle\Exception\ThrottleLimitException;

/**
 * This class is a wrapper for the HTTP client.
 */
class RestClient
{
    /**
     * Your API key.
     *
     * @var string
     */
    private $apiKey;
    private $httpClient;
    private $apiHost;
    private $cache;

    public function __construct(string $apiKey, string $apiHost, ?HttpClient $httpClient = null, ?CacheItemPoolInterface $cache = null)
    {
        $this->apiKey     = $apiKey;
        $this->apiHost    = $apiHost;
        $this->httpClient = $httpClient;
        $this->cache      = $cache;
    }

    protected function getHttpClient(): HttpClient
    {
        if (null === $this->httpClient) {
            $this->httpClient = HttpClientDiscovery::find();
        }

        $authentication = new QueryParam(['key' => $this->apiKey]);

        $authenticationPlugin = new AuthenticationPlugin($authentication);

        $client = new PluginClient($this->httpClient, [$authenticationPlugin]);

        return $client;
    }

    /**
     * Sends the API request if cache not hit
     *
     * @param array<string,string> $headers
     * @param mixed                $body
     *
     * @return array<mixed>
     */
    public function send(string $method, string $uri, $body = null, array $headers = [], ?string $cacheKey = null, ?int $ttl = null): array
    {
        $saveToCache = false;

        if (null !== $cacheKey && null !== $ttl && null !== $this->cache) {
            if ($this->cache->hasItem($cacheKey)) {
                return $this->cache->getItem($cacheKey)->get();
            } else {
                $saveToCache = true;
            }
        }

        if (is_array($body)) {
            $body                    = http_build_query($body);
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        $request     = MessageFactoryDiscovery::find()->createRequest($method, $this->getApiUrl($uri), $headers, $body);
        $rawResponse = $this->getHttpClient()->sendRequest($request);

        $data = $this->processResponse($rawResponse);

        if (null !== $this->cache && null !== $cacheKey && true === $saveToCache) {
            $item = $this->cache
                ->getItem($cacheKey)
                ->set($data)
                ->expiresAfter($ttl)
            ;

            $this->cache->save($item);
        }

        return $data;
    }

    /**
     * Process the API response, provides error handling
     *
     * @throws \Exception
     *
     * @return array<string,mixed>
     */
    public function processResponse(ResponseInterface $response): array
    {
        $data         = (string) $response->getBody();
        $responseData = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($responseData)) {
            throw new \UnexpectedValueException(sprintf('Expected "array" as Response content, got "%s", instead.', gettype($responseData)));
        }

        if (isset($responseData['status']) && 'success' != $responseData['status'] && array_key_exists('message', $responseData)) {
            throw new \Exception(sprintf('MyPoseo API: %s', $responseData['message']));
        }

        if (isset($responseData['myposeo']['code'])) {
            if ('-1' == $responseData['myposeo']['code'] && 'No enough credits' == $responseData['myposeo']['message']) {
                throw new NotEnoughCreditsException();
            }

            if ('-1' == $responseData['myposeo']['code']) {
                throw new ThrottleLimitException();
            }
        }

        return $responseData;
    }

    /**
     * @param array<string,mixed> $queryString
     *
     * @return array<mixed>
     */
    public function get(string $endpointUrl, array $queryString = [], ?string $cacheKey = null, ?int $ttl = null): array
    {
        return $this->send('GET', $endpointUrl.'?'.http_build_query($queryString), null, [], $cacheKey, $ttl);
    }

    /**
     * @param array<string,mixed> $postData
     *
     * @return array<mixed>
     */
    public function post(string $endpointUrl, array $postData = []): array
    {
        $postDataMultipart = [];
        foreach ($postData as $key => $value) {
            if (is_array($value)) {
                $index = 0;
                foreach ($value as $subValue) {
                    $postDataMultipart[] = [
                        'name'     => sprintf('%s[%d]', $key, $index++),
                        'contents' => $subValue,
                    ];
                }
            } else {
                $postDataMultipart[] = [
                    'name'     => $key,
                    'contents' => $value,
                ];
            }
        }

        return $this->send('POST', $endpointUrl, $postDataMultipart);
    }

    private function getApiUrl(string $uri): string
    {
        return $this->generateEndpoint($this->apiHost).$uri;
    }

    private function generateEndpoint(string $apiEndpoint): string
    {
        return sprintf('%s/', $apiEndpoint);
    }
}
