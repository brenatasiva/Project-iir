<?php

namespace RWC\TwitterStream\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;

class TwitterException extends Exception
{
    public static function fromClientException(ClientException $exception): void
    {
        $response = json_decode($exception->getResponse()->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (array_key_exists('detail', $response)) {
            throw new self($response['detail']);
        }

        throw new self(implode(PHP_EOL, array_map(fn ($error) => $error['message'], $response['errors'])));
    }
}
