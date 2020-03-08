<?php
namespace App\Form;

use App\Entity\Grade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class GradeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', null, array('label' => 'Code', 'invalid_message' => 'Le champ est incorrect'))
            ->add('libelleCourt', null, array('label' => 'Libellé court', 'invalid_message' => 'Le champ est incorrect'))
            ->add('libelleLong', null, array('label' => 'Libellé long', 'invalid_message' => 'Le champ est incorrect'))
            ->add('categorie', null, array('label' => 'Catégorie', 'invalid_message' => 'Le champ est incorrect'))
            ->add('actif', null, array('label' => 'Actif (O pour Oui, N pour Non)', 'invalid_message' => 'Le champ est incorrect'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Grade::class,
        ]);
    }
}
