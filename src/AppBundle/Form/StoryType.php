<?php
/**
 * Story Form Type
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class StoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content', 'hidden')
            ->add('preview', 'textarea')
            ->add('collection', 'entity', [
                'class' => 'AppBundle:Collection',
                'choice_label' => 'name',
            ])
            ->add('tagsList')
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'story';
    }
}
