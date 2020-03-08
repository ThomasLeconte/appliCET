<?php
namespace App\Form;

use App\Entity\Personnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class PersonnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numen', null, array('label' => 'Numen', 'invalid_message' => 'Le champ est incorrect'))
            ->add('idLdap', null, array('label' => 'Id LDAP', 'invalid_message' => 'Le champ est incorrect'))
            ->add('nom', null, array('label' => 'Nom', 'invalid_message' => 'Le champ est incorrect'))
            ->add('prenom', null, array('label' => 'Prénom', 'invalid_message' => 'Le champ est incorrect'))
            ->add('etatCloture', CheckboxType::class, array('required'=> false, 'label' => 'Etat de clôture'))
            //->add('typeCloture')
            ->add('affectation')
            ->add('grade')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Personnel::class,
        ]);
    }
}
