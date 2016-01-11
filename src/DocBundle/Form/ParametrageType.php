<?php

namespace DocBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('partenaires', EntityType::class, array(
                'class' => 'DocBundle\Entity\Reseau',
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'required' => true
            ))
            ->add('collectivites')
            ->add('contrat')
            ->add('libelle')
            ->add('ordre')
            ->add('type')
            ->add('reference')
           /* ->add('commentaire')
            ->add('updateAt', 'datetime')
            ->add('createdAt', 'datetime')*/
            //->add('pdfSource', PdfType::class)
           // ->add('Valider', SubmitType::class)
           ->add('pdfSource',  PdfType::class)
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
