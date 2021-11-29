<?php

namespace RWC\TwitterStream;

use Error;
use Generator;
use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;
use Throwable;

class TwitterStream
{
    protected TwitterClient $httpClient;
    protected StreamInterface $streamConnection;

    public function __construct(
        protected string $bearerToken,
        protected string $apiKey,
        protected string $apiSecretKey,
    ) {
        $this->httpClient = new TwitterClient(new Client([
            'headers' => [
                'Authorization' => "Bearer {$this->bearerToken}",
            ],
        ]));

        Rule::useHttpClient($this->httpClient);
    }

    public function filteredTweets(Sets $sets = null): Generator
    {
        $this->streamConnection = $this->connectToFilteredStream($sets);
        $shouldKeepListening    = function (): bool {
            try {
                return !$this->streamConnection->eof();
            } catch (Throwable) {
                return false;
            }
        };

        while ($shouldKeepListening()) {
            $char  = $this->streamConnection->read(1);
            $tweet = $char;

            while ($char !== "\n" && $tweet[-1] !== "\r") {
                $char = $this->streamConnection->read(1);
                $tweet .= $char;
            }

            $decoded = json_decode($tweet, true);

            if ($decoded) {
                yield $decoded;
            }
        }
    }

    protected function connectToFilteredStream(Sets $sets = null): StreamInterface
    {
        // Could use the null object pattern
        $sets ??= new Sets();

        return $this->httpClient
            ->stream('GET', 'https://api.twitter.com/2/tweets/search/stream?' . $sets)
            ->getBody();
    }

    public function __destruct()
    {
        // If the connection was never initialized, this throws an error.
        try {
            $this->stopListening();
        } catch (Error) {
        }
    }

    public function stopListening(): self
    {
        $this->streamConnection->close();

        return $this;
    }

    public function reconnect(): self
    {
        $this->stopListening();
        $this->streamConnection = $this->connectToFilteredStream();

        return $this;
    }
}
