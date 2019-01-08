<?php

namespace App\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Filter\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Sonata\AdminBundle\Route\RouteCollection;

class CompanyAdmin extends Admin
{
    protected $datagridValues = array(

        // display the first page (default = 1)
        '_page' => 1,

        // reverse order (default = 'ASC')
        '_sort_order' => 'DESC',

        // name of the ordered field (default = the model's id field, if any)
        '_sort_by' => 'id',
    );

    public function getUser()
    {
        if (!$this->getConfigurationPool()->getContainer()->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, array('label' => 'Name'))
            ->add('email', null, array('label' => 'Email'))
            ->add('phone', null, array('label' => 'Phone'))
            ->add('description', null, array('label' => 'Description'));
    }


    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('label' => 'Name'))
            ->add('email', null, array('label' => 'Email'))
            ->add('phone', null, array('label' => 'Phone'));
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $actions = [];
        $actions['edit'] = [];
        $actions['delete'] = [];
        $listMapper
            ->add('name', null, array('label' => 'Name'))
            ->add('email', null, array('label' => 'Email'))
            ->add('phone', null, array('label' => 'Phone'))
            ->add('description', null, array('label' => 'Description'));
        $listMapper->add('_action', 'actions', ['actions' => $actions]);

    }

    public function prePersist($object)
    {
        parent::prePersist($object);
        $object->setCreatedOn(new \DateTime('now'));
    }



}


