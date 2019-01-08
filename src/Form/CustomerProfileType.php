<?php

namespace App\Form;

use Oh\GoogleMapFormTypeBundle\Form\Type\GoogleMapType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerProfileType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        // TODO: Implement setDefaultOptions() method.
    }

    public function getName()
    {
        return 'customer_profile';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'required' => true,
                'constraints' => array(new Length(array('min' => 1)))
            ))
            ->add('email', TextType::class, array(
                'required' => true,
                'constraints' => array(new NotBlank())
            ))
            ->add('phone', TextType::class, array(
                'required' => true,
                'constraints' => array(new Length(array('min' => 5)))
            ))
            ->add('longitude', HiddenType::class)
            ->add('latitude', HiddenType::class)
            ->add('update', SubmitType::class);
    }
}