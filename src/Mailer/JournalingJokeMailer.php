<?php declare(strict_types=1);

namespace App\Mailer;

use App\Entity\Joke;
use App\Form\Model\RandomJokeRequest;
use App\Journal\JokeJournalInterface;

class JournalingJokeMailer implements JokeMailerInterface
{
    /** @var JokeMailerInterface */
    protected $mailer;

    /** @var JokeJournalInterface */
    protected $journal;

    public function __construct(JokeMailerInterface $jokeMailer, JokeJournalInterface $journal)
    {
        $this->mailer = $jokeMailer;
        $this->journal = $journal;
    }

    /**
     * Отправить письмо со случайной шуткой
     * @param RandomJokeRequest $request
     * @param Joke $joke
     * @return void
     */
    public function sendRandomJokeEmailMessage(RandomJokeRequest $request, Joke $joke): void
    {
        $this->mailer->sendRandomJokeEmailMessage($request, $joke);
        $this->journal->add($joke);
    }
}