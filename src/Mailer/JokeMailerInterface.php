<?php

namespace App\Mailer;

use App\Entity\Joke;
use App\Entity\RandomJokeRequest;

interface JokeMailerInterface
{
    /**
     * Отправить письмо со случайной шуткой
     * @param RandomJokeRequest $request
     * @param Joke $joke
     * @return void
     */
    public function sendRandomJokeEmailMessage(RandomJokeRequest $request, Joke $joke): void;
}