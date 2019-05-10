<?php declare(strict_types=1);

namespace App\Mailer;

use App\Entity\Joke;
use App\Form\Model\RandomJokeRequest;

class JokeMailer implements JokeMailerInterface
{
    /** @var \Swift_Mailer */
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Отправить письмо со случайной шуткой
     * @param RandomJokeRequest $request
     * @param Joke $joke
     * @return void
     */
    public function sendRandomJokeEmailMessage(RandomJokeRequest $request, Joke $joke): void
    {
        $message = (new \Swift_Message())
            ->setSubject("Случайная шутка из {$request->getCategory()}")
            ->setFrom('noreply@noreply.ru')
            ->setTo($request->getEmail())
            ->setBody($joke->getText())
        ;

        $this->mailer->send($message);
    }
}