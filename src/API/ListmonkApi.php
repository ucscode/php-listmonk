<?php

namespace Junisan\ListmonkApi\API;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Junisan\ListmonkApi\Exceptions\ApiClientException;
use Junisan\ListmonkApi\Exceptions\ApiException;
use Junisan\ListmonkApi\Exceptions\ApiServerException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Client\ClientInterface;

class ListmonkApi
{
    private ClientInterface $client;
    private string $url;
    private string $username;
    private string $password;

    public function __construct(string $url, ?string $username = null, ?string $password = null, ?ClientInterface $client = null)
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        $this->client = $client ?? new Client();
    }

    public function get(string $path)
    {
        return $this->request('get', $path, []);
    }

    public function post(string $path, array $data)
    {
        $guzzleData = [
            'json' => $data,
        ];

        return $this->request('post', $path, $guzzleData);
    }

    public function put(string $path, array $data)
    {
        $guzzleData = [
            'json' => $data,
        ];

        return $this->request('put', $path, $guzzleData);
    }

    /**
     * @throws ApiClientException
     * @throws ApiException
     * @throws GuzzleException
     * @throws ApiServerException
     */
    protected function request(string $method, string $path, array $data): array
    {
        $url = $this->url . $path;
        if (!$data) {
            $data = [];
        }

        if ($this->username) {
            $data['auth'] = [$this->username, $this->password];
        }

        try {
            $response = $this->client->request($method, $url, $data);

            //Preview feature does not response with default schema. Returns response without processing it
            if ($this->isCampaignPreviewPath($path)) {
                return ['preview' => $response->getBody()->getContents() ];
            }

            $json = json_decode($response->getBody()->getContents(), true);
            if (JSON_ERROR_NONE === json_last_error() && array_key_exists('data', $json)) {
                return $json['data'];
            } else {
                throw new ApiException('Unknown API response format');
            }
        } catch (ClientException $e) {
            //Client send invalid data or not found
            $message = $this->error2message($e, 'Client');
            throw new ApiClientException($message);
        } catch (ServerException $e) {
            //Server is broken
            $message = $this->error2message($e, 'Server');
            throw new ApiServerException($message);
        } catch (TransferException $e) {
            //Error in network
            throw new ApiException($e->getMessage());
        } catch (\Exception $e) {
            //WTF ??
            throw new ApiException($e->getMessage());
        }
    }

    protected function isCampaignPreviewPath(string $path)
    {
        $pattern = '@^/campaigns/\d+/preview$@';
        return preg_match($pattern, $path);
    }

    protected function error2message(RequestException $e, string $agent)
    {
        if (!$e->hasResponse()) {
            return 'Invalid response or no response was given from server';
        }

        $body = $e->getResponse()->getBody()->getContents();
        $json = json_decode($body, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $errorMessage = $json['message'] ?? $json['error'] ?? null;

            if ($errorMessage) {
                return sprintf("%s Error: %s", $agent, $errorMessage);
            }
        }

        // 2. Fallback: Parse HTML using native DOMDocument
        $dom = new \DOMDocument();

        // Suppress warnings for malformed HTML common in 404/500 pages
        libxml_use_internal_errors(true);
        $dom->loadHTML($body, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Look for the most descriptive tags in order of priority
        // We target <h1> (main error), then <title> (page status)
        $nodes = $xpath->query('//h1 | //title');

        $errorMessage = 'Unknown HTML Error';

        if ($nodes->length > 0) {
            // Get the first match (usually H1 if present, otherwise Title)
            $errorMessage = trim($nodes->item(0)->nodeValue);
        }

        // Clean up any remaining artifacts and truncate if still too long
        $errorMessage = mb_strimwidth($errorMessage, 0, 150, '...');

        return sprintf("%s Error: %s", $agent, $errorMessage);
    }
}
