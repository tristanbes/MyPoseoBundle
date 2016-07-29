<?PHP

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Connection;

use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Psr\Http\Message\ResponseInterface;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Message\Authentication\QueryParam;

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
     * The version of the API to use.
     *
     * @var string
     */
    protected $apiVersion = 'v2';

    /**
     * If we should use SSL or not.
     *
     * @var bool
     */
    protected $sslEnabled = true;

    /**
     * @param string     $apiKey
     * @param string     $apiHost
     * @param HttpClient $httpClient
     */
    public function __construct($apiKey, $apiHost, $version, HttpClient $httpClient = null)
    {
        $this->apiKey     = $apiKey;
        $this->apiHost    = $apiHost;
        $this->apiVersion = $version;
        $this->httpClient = $httpClient;
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
     * @param RequestInterface $request
     * @param string           $cacheKey
     * @param integer          $ttl
     *
     * @throws NotEnoughCreditsException
     *
     * @return Response
     */
    public function send($method, $uri, $body = null, array $headers = [], $cacheKey = null, $ttl = null)
    {
        $saveToCache = false;

        if ($cacheKey && $ttl && $this->cache) {
            if ($this->cache->contains($cacheKey)) {
                return $this->cache->fetch($cacheKey);
            } else {
                $saveToCache = true;
            }
        }

        $request = MessageFactoryDiscovery::find()->createRequest($method, $this->getApiUrl($uri), $headers, $body);
        $rawResponse = $this->getHttpClient()->sendRequest($request);

        $response = $this->processResponse($rawResponse);

        if (true === $saveToCache) {
            $this->cache->save($cacheKey, $response, $ttl);
        }

        return $response;
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
        $httpResponseCode = (int) $response->getStatusCode();

        $data = (string) $response->getBody();
        $jsonResponseData = json_decode($data, false);

        $result = new \stdClass();
        // return response data as json if possible, raw if not
        $result->http_response_body = $data && $jsonResponseData === null ? $data : $jsonResponseData;
        $result->http_response_code = $httpResponseCode;

        if (isset($jsonResponseData['status']) && $jsonResponseData['status'] != "success") {
            throw new \Exception('MyPoseo API: '.$data['message']);
        }

        if ($jsonResponseData['myposeo']['code'] == '-1' && $jsonResponseData['myposeo']['message'] == 'No enough credits') {
            throw new NotEnoughCreditsException();
        }

        return $jsonResponseData;
    }

    /**
     * @param string $endpointUrl
     * @param array  $queryString
     * @param null   $cacheKey
     * @param null   $ttl
     *
     * @return ResponseInterface
     */
    public function get($endpointUrl, $queryString = [], $cacheKey = null, $ttl = null)
    {
        return $this->send('GET', $endpointUrl.'?'.http_build_query($queryString), null, [], $cacheKey, $ttl);
    }

    /**
     * @param $uri
     *
     * @return string
     */
    private function getApiUrl($uri)
    {
        return $this->generateEndpoint($this->apiHost, $this->apiVersion).$uri;
    }

    /**
     * @param string $apiEndpoint
     * @param string $apiVersion
     *
     * @return string
     */
    private function generateEndpoint($apiEndpoint, $apiVersion)
    {
        $apiUrl = strtr($apiEndpoint, ['{version}' => $apiVersion]);

        return sprintf('%s/', $apiUrl);
    }
}
