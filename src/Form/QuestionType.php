<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('questiontext')
            ->add('questiontype', ChoiceType::class, [
                'choices' => [
                    'Texte' => 'text',
                    'Choix multiple' => 'checkbox',
                    'Choix unique' => 'radio',
                    'Liste déroulante' => 'dropdown',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            //->add('questionnaireid', HiddenType::class, [
            //    'data' => $options['questionnaireid'], // Pré-remplit le champ avec l'ID du questionnaire transmis en option
            //])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
