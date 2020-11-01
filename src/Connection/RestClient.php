<?PHP

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Connection;

use Http\Client\HttpClient;
use Http\Client\Common\PluginClient;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\Authentication\QueryParam;
use Http\Client\Common\Plugin\AuthenticationPlugin;

use Tristanbes\MyPoseoBundle\Exception\ThrottleLimitException;
use Tristanbes\MyPoseoBundle\Exception\NotEnoughCreditsException;

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

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiHost;

    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @param string                 $apiKey
     * @param string                 $apiHost
     * @param HttpClient             $httpClient
     * @param CacheItemPoolInterface $cache
     */
    public function __construct($apiKey, $apiHost, $httpClient = null, CacheItemPoolInterface $cache = null)
    {
        $this->apiKey     = $apiKey;
        $this->apiHost    = $apiHost;
        $this->httpClient = $httpClient;
        $this->cache      = $cache;
    }

     /**
     * @return HttpClient
     */
    protected function getHttpClient()
    {
        if ($this->httpClient === null) {
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
     * @param string  $method
     * @param string  $uri
     * @param null    $body
     * @param array   $headers
     * @param string  $cacheKey
     * @param integer $ttl
     *
     * @return array
     */
    public function send($method, $uri, $body = null, array $headers = [], $cacheKey = null, $ttl = null)
    {
        $saveToCache = false;

        if ($cacheKey !== null && $ttl !== null && $this->cache) {
            if ($this->cache->hasItem($cacheKey)) {
                return $this->cache->getItem($cacheKey)->get();
            } else {
                $saveToCache = true;
            }
        }

        if (is_array($body)) {
            $body = http_build_query($body);
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        $request = MessageFactoryDiscovery::find()->createRequest($method, $this->getApiUrl($uri), $headers, $body);
        $rawResponse = $this->getHttpClient()->sendRequest($request);

        $data = $this->processResponse($rawResponse);

        if ($this->cache && true === $saveToCache) {
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
     * @param ResponseInterface $response
     *
     * @throws \Exception
     *
     * @return array
     */
    public function processResponse(ResponseInterface $response)
    {
        $data = (string) $response->getBody();
        $responseData = json_decode($data, true);

        if (isset($responseData['status']) && $responseData['status'] != "success" && array_key_exists('message', $responseData)) {
            throw new \Exception(sprintf('MyPoseo API: %s', $responseData['message']));
        }

        if (isset($responseData['myposeo']['code'])) {
            if ($responseData['myposeo']['code'] == '-1' && $responseData['myposeo']['message'] == 'No enough credits') {
                throw new NotEnoughCreditsException();
            }

            if ($responseData['myposeo']['code'] == '-1') {
                throw new ThrottleLimitException();
            }
        }

        return $responseData;
    }

    /**
     * @param string       $endpointUrl
     * @param array        $queryString
     * @param string|null  $cacheKey
     * @param integer|null $ttl
     *
     * @return ResponseInterface
     */
    public function get($endpointUrl, $queryString = [], $cacheKey = null, $ttl = null)
    {
        return $this->send('GET', $endpointUrl.'?'.http_build_query($queryString), null, [], $cacheKey, $ttl);
    }

    /**
     * @param string $endpointUrl
     * @param array  $postData
     *
     * @return \stdClass
     */
    public function post($endpointUrl, array $postData = [])
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

    /**
     * @param $uri
     *
     * @return string
     */
    private function getApiUrl($uri)
    {
        return $this->generateEndpoint($this->apiHost).$uri;
    }

    /**
     * @param string $apiEndpoint
     *
     * @return string
     */
    private function generateEndpoint($apiEndpoint)
    {
        return sprintf('%s/', $apiEndpoint);
    }
}
