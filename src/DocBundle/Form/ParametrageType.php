<?php

namespace DocBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametrageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reseau', EntityType::class, array(
                'class' => 'DocBundle\Entity\Reseau',
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'placeholder' => 'Choisir le réseau',
            ))
            ->add('partenaires', TextType::class, array(
                  'data' => 'Tous',
                )
            )
            ->add('collectivites')
            ->add('contrat')
            ->add('libelle')
            ->add('ordre')
            ->add('reference')
            ->add('liasse', CheckboxType::class,  array(
                'label'    => 'Liasse',
                'required' => false,))
            ->add('type', TextType::class)
            ->add('currentPdf',  PdfType::class)
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DocBundle\Entity\Parametrage'
        ));
    }
}
