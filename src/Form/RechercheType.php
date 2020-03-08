<?php

namespace App\Form;

use App\Tool\Interfaces\IRecherche;
use App\Tool\Recherche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, ['required'=>true])
            ->add('prenom', null, ['required'=>false])
            ->add('nomPatronymique', null, ['required'=>false])
            ->add('rne', null, ['required'=>false])
            ->add('dateAction', DateType::class, array('widget'=> 'single_text', 'format'=>'dd/MM/yyyy', 'attr' => array('class' => 'date'), 'label' => 'Dernière action', 'invalid_message' => 'Le format de la date est incorrect', 'required'=>false))
            ->add('filtre', ChoiceType::class, [
                'choices' => [
                    '' => IRecherche::CET_NUL,
                    'Ouvert' => IRecherche::CET_OUVERT,
                    'Fermé' => IRecherche::CET_FERME
                ], 'label' => 'CET ouvert', 'invalid_message' => 'Le champ est incorrect', 'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recherche::class,
        ]);
    }
}
