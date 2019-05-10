<?php

namespace App\Client;

use App\Entity\Joke;
use App\Exception\JokeSourceClientException;

interface JokeSourceClientInterface
{
    /**
     * Возвращает случайную шутку
     * @param string[] $categories
     * @return Joke
     * @throws JokeSourceClientException
     */
    public function getRandomJoke(array $categories = []): Joke;

    /**
     * Возвращает список категорий
     * @return string[]
     * @throws JokeSourceClientException
     */
    public function getCategories(): array;
}