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

class CategoryContentExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content',
                'textarea',
                array(
                    'label' => 'カテゴリ別表示用コンテンツ',
                    'mapped' => false,
                )
            );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
    }

    public function getExtendedType()
    {
        return 'admin_category';
    }
}