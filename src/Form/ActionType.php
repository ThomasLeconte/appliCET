<?php
namespace App\Form;

use App\Entity\Action;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('annee')
            ->add('date',null, array('widget'=> 'single_text', 'format'=>'dd/MM/yyyy', 'attr' => array('class' => 'date'), 'label' => 'Date', 'invalid_message' => 'Le format de la date est incorrect'))
            ->add('jours', null, array('label' => 'Jours', 'invalid_message' => 'Le champ est incorrect'))
            ->add('conges', null, array('label' => 'Conges', 'invalid_message' => 'Le champ est incorrect'))
            ->add('personnel')
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
