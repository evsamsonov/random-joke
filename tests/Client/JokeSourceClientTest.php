<?php declare(strict_types=1);

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
    public function testGetCategories(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"type":"success","value":["explicit","nerdy"]}'),
        ]);

        $handler = HandlerStack::create($mock);
        $client = Client::createWithConfig(['handler' => $handler]);
        $jokeSourceClient = new JokeSourceClient($client);

        $categories = $jokeSourceClient->getCategories();
        $this->assertIsArray($categories, print_r($categories, true));
        $this->assertCount(2, $categories, print_r($categories, true));
    }

    /**
     * @dataProvider failGetCategoriesResponseProvider
     * @param Response $response
     */
    public function testGetCategoriesFail(Response $response): void
    {
        $this->expectException(JokeSourceClientException::class);

        $mock = new MockHandler([$response]);
        $handler = HandlerStack::create($mock);
        $client = Client::createWithConfig(['handler' => $handler]);

        $jokeSourceClient = new JokeSourceClient($client);
        $jokeSourceClient->getCategories();
    }

    /**
     * @return iterable
     */
    public function failGetCategoriesResponseProvider()
    {
        yield [new Response(500, [], '')];
        yield [new Response(200, [], '')];
        yield [new Response(200, [], '{"type":"fail"}')];
        yield [new Response(200, [], '{"type":"success"}')];
        yield [new Response(200, [], '{"type":"success","value": ""}')];
    }

    public function testGetRandomJoke(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"type":"success","value":{"id":100,"joke":"A joke about Jack Norris","categories":[]}}'),
        ]);

        $handler = HandlerStack::create($mock);
        $client = Client::createWithConfig(['handler' => $handler]);
        $jokeSourceClient = new JokeSourceClient($client);

        $randomJoke = $jokeSourceClient->getRandomJoke(['nerdy']);
        $this->assertEquals(100, $randomJoke->getId(), (string)$randomJoke->getId());
        $this->assertEquals('A joke about Jack Norris', $randomJoke->getText());
        $this->assertEmpty($randomJoke->getCategories(), print_r($randomJoke->getCategories(), true));
    }

    /**
     * @dataProvider failGetRandomJokeResponseProvider
     * @param Response $response
     */
    public function testGetRandomJokeFail(Response $response): void
    {
        $this->expectException(JokeSourceClientException::class);

        $mock = new MockHandler([$response]);
        $handler = HandlerStack::create($mock);
        $client = Client::createWithConfig(['handler' => $handler]);

        $jokeSourceClient = new JokeSourceClient($client);
        $jokeSourceClient->getRandomJoke();
    }

    /**
     * @return iterable
     */
    public function failGetRandomJokeResponseProvider(): iterable
    {
        yield [new Response(500, [], '')];
        yield [new Response(200, [], '')];
        yield [new Response(200, [], '{"type":"fail"}')];
        yield [new Response(200, [], '{"type":"success"}')];
        yield [new Response(200, [], '{"type":"success","value": ""}')];
        yield [new Response(200, [], '{"type":"success","value":{"id":"a","joke":"A joke about Jack Norris","categories":[]}}')];
    }
}