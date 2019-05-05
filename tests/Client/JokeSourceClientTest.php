<?php

namespace App\Tests\Client;

use App\Client\JokeSourceClient;
use App\Exception\JokeSourceClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Http\Adapter\Guzzle6\Client;
use PHPUnit\Framework\TestCase;

class JokeSourceClientTest extends TestCase
{
    /**
     * @throws JokeSourceClientException
     */
    public function testGetCategories(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"type":"success","value":["explicit","nerdy"]}'),
            new Response(500, [], ''),
            new Response(200, [], ''),
            new Response(200, [], '{"type":"fail"}'),
            new Response(200, [], '{"type":"success"}'),
            new Response(200, [], '{"type":"success","value": ""}'),
        ]);

        $handler = HandlerStack::create($mock);
        $client = Client::createWithConfig(['handler' => $handler]);

        $jokeSourceClient = new JokeSourceClient('', $client);

        $categories = $jokeSourceClient->getCategories();
        $this->assertIsArray($categories, print_r($categories, true));
        $this->assertCount(2, $categories, print_r($categories, true));

        for($i = 0; $i < 5; $i++) {
            $this->assertException(JokeSourceClientException::class, function () use ($jokeSourceClient) {
                $jokeSourceClient->getCategories();
            });
        }
    }

    /**
     * @throws JokeSourceClientException
     */
    public function testGetRandomJoke(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"type":"success","value":{"id":100,"joke":"A joke about Jack Norris","categories":[]}}'),
            new Response(500, [], ''),
            new Response(200, [], ''),
            new Response(200, [], '{"type":"fail"}'),
            new Response(200, [], '{"type":"success"}'),
            new Response(200, [], '{"type":"success","value": ""}'),
            new Response(200, [], '{"type":"success","value":{"id":"  ","joke":"A joke about Jack Norris","categories":[]}}'),
        ]);

        $handler = HandlerStack::create($mock);
        $client = Client::createWithConfig(['handler' => $handler]);

        $jokeSourceClient = new JokeSourceClient('', $client);

        $randomJoke = $jokeSourceClient->getRandomJoke(['nerdy']);
        $this->assertEquals(100, $randomJoke->getId(), $randomJoke->getId());
        $this->assertEquals('A joke about Jack Norris', $randomJoke->getText());
        $this->assertEmpty($randomJoke->getCategories(), print_r($randomJoke->getCategories(), true));

        for($i = 0; $i < 6; $i++) {
            $this->assertException(JokeSourceClientException::class, function () use ($jokeSourceClient) {
                $jokeSourceClient->getRandomJoke();
            });
        }
    }

    /**
     * @param string $expectClass
     * @param callable $callback
     */
    protected function assertException(string $expectClass, callable $callback): void
    {
        try {
            $callback();
        } catch (\Throwable $exception) {
            $this->assertInstanceOf($expectClass, $exception, 'An invalid exception was thrown');
            return;
        }

        $this->fail('No exception was thrown');
    }
}