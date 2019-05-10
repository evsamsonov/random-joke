<?php declare(strict_types=1);

namespace App\Form\Type;

use App\Client\JokeSourceClientInterface;
use App\Form\Model\RandomJokeRequest;
use App\Exception\JokeSourceClientException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RandomJokeRequestType extends AbstractType
{
    /** @var JokeSourceClientInterface */
    protected $jokeSourceClient;

    public function __construct(JokeSourceClientInterface $jokeSourceClient)
    {
        $this->jokeSourceClient = $jokeSourceClient;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        try {
            $categories = $this->jokeSourceClient->getCategories();
        } catch (JokeSourceClientException $e) {
            $categories = [];
        }

        $builder
            ->add('email')
            ->add('category', ChoiceType::class, [
                'choices' => array_combine($categories, $categories)
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RandomJokeRequest::class,
        ]);
    }
}