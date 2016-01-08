<?php

namespace DocBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CnpDocumentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('partenaires')
            ->add('collectivites')
            ->add('contrat')
            ->add('libelle')
            ->add('ordre')
            ->add('type')
            ->add('reference')
           /* ->add('commentaire')
            ->add('updateAt', 'datetime')
            ->add('createdAt', 'datetime')*/
            ->add('pdfSource', PdfType::class)
           // ->add('Valider', SubmitType::class)
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
