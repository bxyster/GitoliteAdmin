<?php

namespace Jmoati\Gitolite\FrontBundle\Form\Type;

use Jmoati\Gitolite\CoreBundle\Entity\Access;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AccessType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user')
            ->add(
                'access',
                'choice',
                array(
                    'choices' => array(
                        Access::REPORTER  => 'Reporter',
                        Access::DEVELOPER => 'Developer',
                        Access::ADMIN     => 'Admin',
                    ),

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
                'data_class' => 'Jmoati\Gitolite\CoreBundle\Entity\Access'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jmoati_gitolite_frontbundle_Access';
    }
}
