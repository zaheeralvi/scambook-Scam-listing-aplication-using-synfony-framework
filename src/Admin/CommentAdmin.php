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

class CommentAdmin extends Admin
{
    protected $datagridValues = array(

        // display the first page (default = 1)
        '_page' => 1,

        // reverse order (default = 'ASC')
        '_sort_order' => 'DESC',

        // name of the ordered field (default = the model's id field, if any)
        '_sort_by' => 'id',
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('user', null, array('required' => true, 'label' => 'User'))
            ->add('post', null, array('required' => true, 'label' => 'post'))
            ->add('commentDetail', null, array('required' => true, 'label' => 'Comment Details'));

    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('commentDetail', null, array('label' => 'Name'));
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $actions = [];
        $actions['edit'] = [];
        $listMapper
            ->add('post', null, array('label' => 'Post'))
            ->add('commentDetail', null, array('label' => 'Comment Details'));
        $actions['delete'] = [];
        $listMapper->add('_action', 'actions', ['actions' => $actions]);
    }

    public function prePersist($object)
    {
        parent::prePersist($object);
        $object->setDate(new \DateTime('now'));
    }
}


