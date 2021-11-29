<?php

namespace RWC\TwitterStream;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

class TwitterClient
{
    protected GuzzleClient $httpClient;

    public function __construct(GuzzleClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function stream(string $method, UriInterface | string $uri = '', array $options = []): ResponseInterface
    {
        $options['stream'] = true;

        return $this->httpClient->request($method, $uri, $options);
    }

    public function request(string $method, UriInterface | string $uri = '', array $options = []): array
    {
        $response = $this->httpClient->request($method, $uri, $options);

        $decoded = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (array_key_exists('errors', $decoded)) {
            throw new RuntimeException($this->getErrorMessageFromResponse($decoded));
        }

        return $decoded;
    }

    protected function getErrorMessageFromResponse(array $decoded): string
    {
        $error = $decoded['errors'][0];

        if (array_key_exists('details', $error)) {
            return sprintf('%s: %s (%s)', $error['title'], implode('. ', $error['details']), $error['type']);
        }

        return sprintf('%s (%s)', $error['title'], $error['type']);
    }
}
