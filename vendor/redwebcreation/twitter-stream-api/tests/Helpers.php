<?php


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use JetBrains\PhpStorm\ArrayShape;

function useHttpClient(array $responses, &$container): Client
{
    $mock = new MockHandler(
        array_map(
            fn($response) => new Response(200, ['Content-Type' => 'application/json'], is_array($response) ? json_encode($response) : $response),
            $responses
        )
    );
    $history = Middleware::history($container);
    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push($history);
    return new Client(['handler' => $handlerStack]);
}