<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\CategoryContent\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CategoryContentExtension
 * @package Plugin\CategoryContent\Form\Extension
 */
class CategoryContentExtension extends AbstractTypeExtension
{
    private $config;
    private $app;

    /**
     * CategoryContentExtension constructor.
     * @param array $config
     * @param array $app
     */
    public function __construct($config, $app)
    {
        $this->config = $config;
        $this->app = $app;
    }

    /**
     * buildForm
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content',
                'textarea',
                array(
                    'label' => 'カテゴリ別表示用コンテンツ',
                    'mapped' => false,
                    'constraints' => array(
                        new Assert\Length(array(
                            'max' => $this->config['category_text_area_len'],
                        )),
                    ),
                    'attr' => array(
                        'maxlength' => $this->config['category_text_area_len'],
                        'placeholder' => $this->app->trans('admin.plugin.category.content'),
                    ),
                )
            );
    }

    /**
     * buildView
     *
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
    }

    /**
     * getExtendedType
     *
     * @return string
     */
    public function getExtendedType()
    {
        return 'admin_category';
    }
}
