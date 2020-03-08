<?php
namespace App\Form;

use App\Entity\LieuAffectation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class LieuAffectationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rne', null, array('label' => 'Rne', 'invalid_message' => 'Le champ est incorrect'))
            ->add('libelle', null, array('label' => 'Libellé', 'invalid_message' => 'Le champ est incorrect'))
            ->add('secteur', null, array('label' => 'Secteur', 'invalid_message' => 'Le champ est incorrect'))
            ->add('sigle', null, array('label' => 'Sigle', 'invalid_message' => 'Le champ est incorrect'))
            ->add('localite', null, array('label' => 'Localité', 'invalid_message' => 'Le champ est incorrect'))
            ->add('typeEtablissement', null, array('label' => 'Type', 'invalid_message' => 'Le champ est incorrect'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LieuAffectation::class,
        ]);
    }
}
