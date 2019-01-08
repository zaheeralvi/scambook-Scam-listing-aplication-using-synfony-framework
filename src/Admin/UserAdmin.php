<?php

namespace App\Admin;

use App\Entity\Accounts\AccountsPermissions;
use App\Entity\HR\HRPermissions;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserAdmin extends Admin
{
    protected $baseRouteName = 'users';
    protected $baseRoutePattern = 'users';

    protected $datagridValues = array(

        // display the first page (default = 1)
        '_page' => 1,

        // reverse order (default = 'ASC')
        '_sort_order' => 'DESC',

        // name of the ordered field (default = the model's id field, if any)
        '_sort_by' => 'id',
    );

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        return $query;
    }

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

    protected function configureRoutes(RouteCollection $collection)
    {
//        $this->
        /*$user = $this->getUser();
        if ($user) {
            if (!$user->getPerUserCreate()) {
                $collection->remove('create');
            }
        }
        $this->getRoutes();*/
        // to remove a single route

    }

//    public function isGranted($name, $object = null)
//    {
//        $user = $this->getUser();
//        switch ($name) {
//            case "CREATE":
//                if (!$user->getPerUserCreate()) {
//                    return false;
//                }
//            default:
//                return true;
//        }
//    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->configureRoutes($this->getRoutes());
        $loginId = '';
        $employeeId = '';
        if ($this->getSubject()->getId()) {
            $loginId = $this->getSubject()->getUserName();
        }
        $formMapper
            ->with("User Info")
            ->add('userName', TextType::class, array('required' => true, 'label' => 'Login Id', 'data' => $loginId))
            ->add('email', EmailType::class, array('required' => true, 'label' => 'Email'))
            ->add('firstName', TextType::class, array('required' => true, 'label' => 'Full Name'))
            ->add('plainPassword', TextType::class, array(
                'label' => 'New password (empty filed means no changes)',
                'required' => FALSE
            ))

            ->add("enabled", CheckboxType::class, array('required' => false, "label" => "Status(is active)"))
            ->end();

    }

    public function prePersist($object)
    {
        parent::prePersist($object);
        $this->updateUser($object);
    }


    public function preUpdate($object)
    {
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.default_entity_manager');
//        $object->setEmail($object->getUsername());
        parent::preUpdate($object);
        $this->updateUser($object);
    }

    public function updateUser(\App\Entity\User $u)
    {
        if ($u->getPlainPassword()) {
            $u->setPlainPassword($u->getPlainPassword());
        }

        $um = $this->getConfigurationPool()->getContainer()->get('fos_user.user_manager');
        $um->updateUser($u, false);
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username', null, array('label' => 'Login ID'))
            ->add('email', null, array('label' => 'Email'))
            ->add('firstName', null, array('label' => 'Full Name'))
            ->add('language', null, array('label' => 'MAC ADDRESS'))
            ->add('enabled', null, array('label' => 'Is Active'))//                ->add('type',null,array('label'=>'Type'));
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $user = $this->getUser();
        $actions = [];
//        if ($user) {
//            if ($user->getPerUserEdit()) {
                $actions['edit'] = [];
//            }
//            if ($user->getPerUserDelete()) {
                $actions['delete'] = [];
//            }
//        }
//        $actions['entity_board'] = array('template' => 'App:sonata:viewuser_button.html.twig');
        $listMapper
            ->addIdentifier('username', 'text', array('label' => 'Name'))
            ->addIdentifier('email', null, array('label' => 'Email'));
        if (!empty($actions)) {
            $listMapper->add('_action', 'actions', ['actions' => $actions]);
        }


    }


}


