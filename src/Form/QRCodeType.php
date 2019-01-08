<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QRCodeType extends AbstractType
{
    public function __construct(){
//        $this->api_key = $api_key;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Default fields: latitude, longitude
//        $builder
//            ->add($options['qr_code'], $options['type'], array_merge($options['options'], $options['lat_options']));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['data'] = $options['data'];
//        echo '<pre>';
//var_dump($options['data']);die;
//        echo '</pre>';die;
    }

    public function getParent()
    {
        return FormType::class;
    }

    public function getName()
    {
        return 'qr_code_image';
    }

    public function getBlockPrefix()
    {
        return 'qr_code_image';
    }
}
