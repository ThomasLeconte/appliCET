<?php

namespace App\Form;

use App\Entity\Action;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionParPersonnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('annee', null, array('label' => 'Annee', 'invalid_message' => 'Le champ est incorrect'))
            ->add('date',null, array('widget'=> 'single_text', 'format'=>'dd/MM/yyyy', 'attr' => array('class' => 'date'), 'label' => 'Date', 'invalid_message' => 'Le format de la date est incorrect'))
            ->add('jours', null, array('label' => 'Jours', 'invalid_message' => 'Le champ est incorrect'))
            ->add('conges', null, array('label' => 'Conges', 'invalid_message' => 'Le champ est incorrect'))
            ->add('typeAction')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Action::class,
        ]);
    }
}
