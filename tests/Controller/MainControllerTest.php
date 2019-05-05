<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Form;

class MainControllerTest extends WebTestCase
{
    /** @var Client  */
    protected $client;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testRequestRandomJokeEmptyForm(): void
    {
        $form = $this->getForm();

        $crawler = $this->client->submit($form);
        $this->assertCount(1, $crawler->filter('ul:contains("Значение не должно быть пустым")'));
    }

    public function testRequestRandomJokeIncorrectEmail(): void
    {
        $form = $this->getForm();

        $form['random_joke_request[email]'] = 'incorrect';

        $crawler = $this->client->submit($form);
        $this->assertCount(1, $crawler->filter('ul:contains("Значение адреса электронной почты недопустимо")'));
    }

    public function testRequestRandomJokeSuccessful(): void
    {
        $form = $this->getForm();

        $form['random_joke_request[email]'] = 'test@test.ru';

        $crawler = $this->client->submit($form);
        $this->assertCount(1, $crawler->filter('div:contains("Успешно отправлена")'));
    }

    /**
     * @return Form
     */
    protected function getForm(): Form
    {
        $crawler = $this->client->request('GET', '/');
        return $crawler->filter('input[type=submit]')->form();
    }
}