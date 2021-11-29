<?php

namespace RWC\TwitterStream;

use GuzzleHttp\Client;
use RuntimeException;

class Rule
{
    protected static ?TwitterClient $httpClient = null;
    protected ?string $id                       = null;
    protected string $value;
    protected string $tag;

    public function __construct(string $value, ?string $tag = null)
    {
        static::ensureHttpClientIsLoaded();
        $this->value = $value;
        $this->tag   = $tag ?? $value;
    }

    protected static function ensureHttpClientIsLoaded(): TwitterClient
    {
        if (static::$httpClient === null) {
            throw new RuntimeException('You need to instantiate a TwitterStream before creating a rule or set the bearer token manually.');
        }

        return static::$httpClient;
    }

    public static function all(): array
    {
        $rules = static::ensureHttpClientIsLoaded()->request('GET', 'https://api.twitter.com/2/tweets/search/stream/rules');

        return array_map(static function ($rawRule) {
            $rule = new self($rawRule['value'], $rawRule['tag']);
            $rule->withId($rawRule['id']);

            return $rule;
        }, $rules['data'] ?? []);
    }

    public function withId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public static function useHttpClient(TwitterClient | Client $client): void
    {
        if ($client instanceof Client) {
            $client = new TwitterClient($client);
        }

        static::$httpClient = $client;
    }

    public static function useBearerToken(string $bearerToken): void
    {
        static::$httpClient = new TwitterClient(new Client([
            'headers' => [
                'Authorization' => "Bearer {$bearerToken}",
            ],
        ]));
    }

    public static function create(string $name, ?string $tag = null): self
    {
        $rule = new self($name, $tag);
        $rule->add();

        return $rule;
    }

    public function add(): array
    {
        $results = static::addBulk($this);

        if (array_key_exists('data', $results)) {
            $this->withId($results['data'][0]['id']);
        }

        return $results;
    }

    public static function addBulk(self ...$rules): array
    {
        return static::bulk(['add' => $rules]);
    }

    public static function bulk(array $operations): array
    {
        $body = [];

        if (array_key_exists('delete', $operations)) {
            $body['delete'] = ['ids' => array_filter(array_map(static fn (Rule $rule) => $rule->getId(), $operations['delete']))];
        }

        if (array_key_exists('add', $operations)) {
            $body['add'] = array_map(static fn (Rule $rule) => ['value' => $rule->getValue(), 'tag' => $rule->getTag()], $operations['add']);
        }

        return static::ensureHttpClientIsLoaded()->request('POST', 'https://api.twitter.com/2/tweets/search/stream/rules', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ]);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function delete(): array
    {
        return static::deleteBulk($this);
    }

    public static function deleteBulk(self ...$rules): array
    {
        if (empty($rules)) {
            return [];
        }

        return static::bulk(['delete' => $rules]);
    }
}
