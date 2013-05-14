<?php

namespace Ripple\AdminBundle\Admin\Entity;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * User admin configuration class.
 *
 * Tells SonataAdminBundle what fields we want for the user class.
 *
 * @package Ripple\AdminBundle\Admin\Entity
 * @author  James Halsall <jhalsall@rippleffect.com>
 */
class UserAdmin extends BaseUserAdmin
{
    /**
     * Configures fields available in the add/edit form.
     *
     * @param FormMapper $formMapper The form mapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->with('General')
                        ->add('forename', 'text', array('label' => 'Forename'))
                        ->add('surname', 'text', array('label' => 'Surname'))
                        ->add('username')
                        ->add('email')
                        ->add('plainPassword', 'password', array('required' => false))
                    ->end()
                    ->with('Groups')
                        ->add('groups', 'sonata_type_model', array('required' => false, 'expanded' => true, 'multiple' => true))
                    ->end();
    }

    /**
     * Configures fields that are available in the data grid.
     *
     * @param DatagridMapper $datagridMapper The datagrid mapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {

    }

    /**
     * Configures fields that are visible in the list view.
     *
     * @param ListMapper $listMapper The list field mapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);
    }

    /**
     * Configures fields that are visible in the "Show" view
     *
     * @param ShowMapper $showMapper The show field mapper
     *
     * @return void
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->with('General')
                       ->add('forename', 'text', array('label' => 'Forename'))
                       ->add('surname', 'text', array('label' => 'Surname'))
                       ->add('username')
                       ->add('email')
                       ->add('plainPassword', 'password', array('required' => false))
                   ->end()
                   ->with('Groups')
                       ->add('groups', 'sonata_type_model', array('required' => false, 'expanded' => true, 'multiple' => true))
                   ->end();
    }
}
