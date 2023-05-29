<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Contracts\Translation\TranslatorInterface;

class AnnonceType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('villeDepart', TextType::class, [
                'label' => $this->translator->trans('form.announce.departure'),
                'attr' => [
                    'class' => 'w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white dark:bg-gray-700',
                ],
            ])
            ->add('villeArrive', TextType::class, [
                'label' => $this->translator->trans('form.announce.arrival'),
                'attr' => [
                    'class' => 'w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm ddark:text-white dark:bg-gray-700',
                ],
            ])
            ->add('prix', NumberType::class, [
                'label' => $this->translator->trans('form.announce.price'),
                'attr' => [
                    'class' => 'w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white dark:bg-gray-700',
                ],
            ])->add('date', DateTimeType::class, [
                'label' => $this->translator->trans('form.announce.date'),
                'widget' => 'single_text',
                'attr' => ['class' => 'w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white dark:bg-gray-700'],
                'constraints' => [
                    new GreaterThan('now'), // Vérifie si la date est postérieure à la date actuelle
                ],
            ])
            ->add('modeleV', TextType::class, [
                'label' => $this->translator->trans('form.announce.car'),
                'attr' => [
                    'class' => 'w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white dark:bg-gray-700',
                ],
            ])
            ->add('nbplace', NumberType::class, [
                'label' => $this->translator->trans('form.announce.people'),
                'attr' => [
                    'class' => 'w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white dark:bg-gray-700',
                ],
            ])
            ->add('connect', SubmitType::class, [
                'label' => $this->translator->trans('form.announce.submit'),
                'attr' => ['class' => 'text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center'],
            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }

}