<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CategoryContent\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CategoryContentExtension
 *
 * @package Plugin\CategoryContent\Form\Extension
 * @deprecated for since v3.0.0, to be removed in 3.1.
 */
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

    public function getExtendedType()
    {
        return 'admin_category';
    }
}