<?php

namespace App\Controller;

use App\Client\JokeSourceClientInterface;
use App\Form\Model\RandomJokeRequest;
use App\Exception\JokeSourceClientException;
use App\Form\Type\RandomJokeRequestType;
use App\Mailer\JokeMailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/")
     * @param Request $request
     * @param JokeSourceClientInterface $jokeSourceClient
     * @param JokeMailerInterface $mailer
     * @return Response
     */
    public function index(Request $request, JokeSourceClientInterface $jokeSourceClient, JokeMailerInterface $mailer): Response
    {
        $jokeRequest = new RandomJokeRequest();
        $form = $this->createForm(RandomJokeRequestType::class, $jokeRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $randomJoke = $jokeSourceClient->getRandomJoke([$jokeRequest->getCategory()]);
                $mailer->sendRandomJokeEmailMessage($jokeRequest, $randomJoke);

                $this->addFlash('notice', 'Успешно отправлена');
            } catch (JokeSourceClientException $e) {
                $form->addError(new FormError('Ошибка при получении данных. Попробуйте позднее'));
            }
        }

        return $this->render('main/index.html.twig', ['form' => $form->createView()]);
    }
}