<?php declare(strict_types=1);

namespace App\Client;

use App\Entity\Joke;
use App\Exception\JokeSourceClientException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use function \GuzzleHttp\Psr7\build_query;

class JokeSourceClient implements JokeSourceClientInterface
{
    /** @var string */
    private const SUCCESS_STATUS = 'success';

    /** @var ClientInterface */
    private $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Возвращает случайные шутки
     * @param array $categories список категорий
     * @return Joke
     * @throws JokeSourceClientException
     */
    public function getRandomJoke(array $categories = []): Joke
    {
        $query = [];
        if (count($categories) > 0) {
            $query['limitTo'] = sprintf('[%s]', implode(',', $categories));
        }

        try {
            /** @var ResponseInterface $response */
            $query = build_query($query);
            $response = $this->httpClient->sendRequest(
                new Request('GET', "/jokes/random?{$query}")
            );
        } catch (ClientExceptionInterface $e) {
            throw new JokeSourceClientException('Ошибка при отправке запроса', 0, $e);
        }

        $response = json_decode($response->getBody()->getContents(), true);
        $this->checkResponse($response);

        try {
            $joke = $response['value'];
            $joke = new Joke($joke['id'], $joke['joke'], $joke['categories']);
        } catch (\Throwable $e) {
            throw new JokeSourceClientException(sprintf('Некорректный формат данных %s', print_r($response, true)));
        }

        return $joke;
    }

    /**
     * Возвращает список категорий
     * @return array
     * @throws JokeSourceClientException
     */
    public function getCategories(): array
    {
        try {
            /** @var ResponseInterface $response */
            $response = $this->httpClient->sendRequest(new Request('GET', "/categories"));
        } catch (ClientExceptionInterface $e) {
            throw new JokeSourceClientException('Ошибка при отправке запроса', 0, $e);
        }

        $response = json_decode($response->getBody()->getContents(), true);
        $this->checkResponse($response);

        if ( ! is_array($response['value'])) {
            throw new JokeSourceClientException(sprintf('Значение value не массив %s', print_r($response, true)));
        }

        return $response['value'];
    }

    /**
     * @param $response
     * @throws JokeSourceClientException
     */
    private function checkResponse($response): void
    {
        if ( ! $response || ! isset($response['type'], $response['value'])) {
            throw new JokeSourceClientException(
                sprintf('Некорректный ответ %s', print_r($response, true))
            );
        }

        if ($response['type'] !== self::SUCCESS_STATUS) {
            throw new JokeSourceClientException(
                sprintf('Неуспешный статус ответа %s', print_r($response, true))
            );
        }
    }
}