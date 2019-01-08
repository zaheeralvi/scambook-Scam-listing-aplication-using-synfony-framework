<?php

namespace App\Admin;

use App\Entity\Attachments;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Filter\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Sonata\AdminBundle\Route\RouteCollection;

class ScamDetailsAdmin extends Admin
{
//    protected $baseRouteName = 'products';
//    protected $baseRoutePattern = 'products';
    protected $productObj;


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
            ->add('company', null, array('required' => true, 'label' => 'Company'))
            ->add('damagePrice', TextType::class, array('required' => true, 'label' => 'Damage Price'))
            ->add('status', ChoiceType::class, array('required' => true, 'label' => 'Status','choices'=>array('new'=>'new','investigation requested'=>'investigation requested','pending'=>'pending','resolved'=>'resolved','No Response from Business'=>'No Response from Business')))
            ->add('investigation', CheckboxType::class, array('required' => false, 'label' => 'Investigation'))
//            ->add('proofFile', TextType::class, array('required' => false, 'label' => 'Proof File'))
            ->add('website', TextType::class, array('required' => false, 'label' => 'Website'))
            ->add('abbreviation', TextType::class, array('required' => false, 'label' => 'Abbreviation'))
            ->add('description', null, array('required' => true, 'label' => 'Description'));
    }


    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('damagePrice', null, array('required' => true, 'label' => 'Damage Price'))
            ->add('status', null, array('required' => true, 'label' => 'Status'))
            ->add('investigation', null, array('required' => true, 'label' => 'Investigation'));
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $actions = [];
        $actions['edit'] = [];
        $actions['delete'] = [];

        $listMapper
            ->add('company.name', TextType::class, array('required' => true, 'label' => 'Company'))
            ->add('damagePrice', TextType::class, array('required' => true, 'label' => 'Damage Price'))
            ->add('status', CheckboxType::class, array('required' => true, 'label' => 'Status'))
            ->add('investigation', CheckboxType::class, array('required' => true, 'label' => 'Investigation'))
            ->add('website', TextType::class, array('required' => false, 'label' => 'Website'))
            ->add('abbreviation', TextType::class, array('required' => false, 'label' => 'Abbreviation'))
            ->add('description', TextType::class, array('required' => true, 'label' => 'Description'));
        $listMapper->add('_action', 'actions', ['actions' => $actions]);
    }

    public function prePersist($object)
    {
        parent::prePersist($object);
        $object->setDateOccurance(new \DateTime('now'));
    }
}


