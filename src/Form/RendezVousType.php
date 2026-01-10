<?php

namespace App\Form;

use App\Entity\RendezVous;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('patient', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getPrenom() . ' ' . $user->getNom() . ' (' . $user->getEmail() . ')';
                },
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_PATIENT%')
                        ->orderBy('u.nom', 'ASC');
                },
                'label' => 'Patient',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le patient est obligatoire']),
                ],
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                ],
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'La date est obligatoire']),
                ],
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                ],
            ])
            ->add('heure', TimeType::class, [
                'label' => 'Heure',
                'widget' => 'single_text',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'L\'heure est obligatoire']),
                ],
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                ],
            ]);

        // Ajouter le champ statut uniquement si l'option 'show_statut' est true (pour l'édition)
        if ($options['show_statut']) {
            $builder->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Programmé' => 'scheduled',
                    'Terminé' => 'completed',
                    'Annulé' => 'cancelled',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le statut est obligatoire']),
                ],
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
            'show_statut' => false, // Par défaut, ne pas afficher le statut (pour la création)
        ]);
    }
}

