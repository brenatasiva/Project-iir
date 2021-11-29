<?php

use GuzzleHttp\Psr7\Request;
use RWC\TwitterStream\Rule;

test('setters and getters', function () {
    Rule::useBearerToken('some token');
    $rule = new Rule('rule', 'tag');
    $rule->withId('1234');
    expect($rule->getValue())->toBe('rule');
    expect($rule->getTag())->toBe('tag');
    expect($rule->getId())->toBe('1234');
});

it('can add a rule', function () {
    $requestsSent = [];
    $client = useHttpClient([
        $returnedPayload = [
            'data' => [
                '0' => [
                    'value' => 'cats has:links',
                    'tag' => 'cats with links',
                    'id' => '1390687625925824521'
                ]
            ],
            'meta' => [
                'sent' => (new DateTime())->format(DateTimeImmutable::RFC3339),
                'summary' => [
                    'created' => 1,
                    'not_created' => 0,
                    'valid' => 1,
                    'invalid' => 0
                ]
            ]
        ]
    ], $requestsSent);
    Rule::useHttpClient($client);

    $rule = new Rule('cats has:images', 'cats with images');
    $response = $rule->add();
    /** @var Request $sentRequest */
    $sentRequest = $requestsSent[0]['request'];

    expect($sentRequest->getMethod())->toBe('POST');
    expect((string)$sentRequest->getUri())->toBe('https://api.twitter.com/2/tweets/search/stream/rules');
    expect($sentRequest->getBody()->getContents())->toBe(json_encode(['add' => [['value' => 'cats has:images', 'tag' => 'cats with images']]]));
    expect($response)->toBe($returnedPayload);
});

it('can delete a rule', function () {
    $requestsSent = [];
    $client = useHttpClient([
        [
            'data' => [
                '0' => [
                    'value' => 'cats has:links',
                    'tag' => 'cats with links',
                    'id' => '1390687625925824521'
                ]
            ],
            'meta' => [
                'sent' => (new DateTime())->format(DateTimeImmutable::RFC3339),
                'summary' => [
                    'created' => 1,
                    'not_created' => 0,
                    'valid' => 1,
                    'invalid' => 0
                ]
            ]
        ],
        ['stub' => true]
    ], $requestsSent);
    Rule::useHttpClient($client);

    $rule = new Rule('cats has:images', 'cats with images');
    $rule->add();
    $rule->delete();
    /** @var Request $sentRequest */
    $sentRequest = $requestsSent[1]['request'];

    expect($sentRequest->getMethod())->toBe('POST');
    expect((string)$sentRequest->getUri())->toBe('https://api.twitter.com/2/tweets/search/stream/rules');
    expect($sentRequest->getBody()->getContents())->toBe(json_encode(['delete' => ['ids' => ['1390687625925824521']]]));
});