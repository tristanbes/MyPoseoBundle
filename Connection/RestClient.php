<?PHP

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\Connection;

use Http\Client\HttpClient;
use Http\Client\Common\PluginClient;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\Authentication\QueryParam;
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

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiHost;

    /**
     * @var null
     */
    protected $cache;

    /**
     * @param string     $apiKey
     * @param string     $apiHost
     * @param HttpClient $httpClient
     */
    public function __construct($apiKey, $apiHost, $httpClient = null, $cache = null)
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
            if ($this->cache->contains($cacheKey)) {
                return $this->cache->fetch($cacheKey);
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
            $this->cache->save($cacheKey, $data, $ttl);
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

        if ($jsonResponseData['myposeo']['code'] == '-1') {
            throw new ThrottleLimitException();
        }

        return $jsonResponseData;
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
