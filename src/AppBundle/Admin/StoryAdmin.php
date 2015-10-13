<?php
/**
 * Story Admin
 */

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class StoryAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text', ['label' => 'Title'])
            ->add('slug', 'text', ['label' => 'Slug'])
            ->add('preview', 'textarea', ['label' => 'Preview'])
            ->add('author', 'entity', ['class' => 'AppBundle\Entity\User'])
            ->add('collection', 'entity', ['class' => 'AppBundle\Entity\Collection'])
            ->add('content', 'textarea', ['label' => 'Content'])
            ->add('tags')
            ->add('published', 'choice', ['label' => 'Published', 'choices' => ['1' => 'Yes', '0' => 'No']])
            ->add('featured', 'choice', ['label' => 'Featured', 'choices' => ['1' => 'Yes', '0' => 'No']])
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('published')
            ->add('featured')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('collection')
            ->add('author')
            ->add('created')
            ->add('published', 'boolean', ['editable' => true])
            ->add('listed', 'boolean', ['editable' => true])
            ->add('featured', 'boolean', ['editable' => true])
        ;
    }
}
