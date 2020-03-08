<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class DataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('etablissements', FileType::class, [
                'required'=>false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                    ])
                ],
            ])

            ->add('grades', FileType::class, [
                'required'=>false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
