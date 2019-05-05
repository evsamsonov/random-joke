<?php

namespace App\Journal;

use App\Entity\Joke;

interface JokeJournalInterface
{
    /**
     * Добавить шутку в журнал
     * @param Joke $joke
     */
    public function add(Joke $joke): void;
}