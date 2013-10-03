<?php

namespace Jmoati\Gitolite\FrontBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RepositoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('description')
            ->add(
                'public',
                'choice',
                array(
                    'label'   => 'Privacy',
                    'choices' => array(
                        1 => 'public',
                        0 => 'private',
                    )
                )
            )
            ->add(
                'website',
                'url',
                array(
                    'required' => false,
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Jmoati\Gitolite\CoreBundle\Entity\Repository'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jmoati_gitolite_frontbundle_repository';
    }
}
