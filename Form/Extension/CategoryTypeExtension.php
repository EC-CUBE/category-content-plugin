<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CategoryContent\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Eccube\Form\Type\Admin\CategoryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;
use Eccube\Common\EccubeConfig;
use Symfony\Component\Translation\TranslatorInterface;
use Plugin\CategoryContent\CategoryContentEvent;

class CategoryTypeExtension extends AbstractTypeExtension
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * CategoryTypeExtension constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EccubeConfig $eccubeConfig,
        TranslatorInterface $translator
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', TextareaType::class, [
            'constraints' => [
                new Assert\Length(['min' => 0, 'max' => $this->eccubeConfig['category_content.category_text_area_len']]),
            ],
            'attr' => [
                'maxlength' => $this->eccubeConfig['category_content.category_text_area_len'],
                'placeholder' => $this->translator->trans('category_content.admin.category.content'),
            ],
        ])->addEventSubscriber(new CategoryContentEvent());
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getExtendedType()
    {
        return CategoryType::class;
    }
}
