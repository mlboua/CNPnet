<?php

namespace DocBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CnpDocumentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('partenaires', TextType::class)
            ->add('collectivites', TextType::class)
            ->add('contrat')
            ->add('libelle')
            ->add('ordre')
            ->add('type')
            ->add('reference')
            ->add('pdfSource', FileType::class, array('label' => 'Brochure (PDF file)'))
            ->add('updateAt', DateType::class)
            ->add('createdAt', DateType::class)
            ->add('commentaire')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DocBundle\Entity\CnpDocument'
        ));
    }
}
