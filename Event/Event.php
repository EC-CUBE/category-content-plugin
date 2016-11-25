<?php
/*
  * This file is part of the CategoryContent plugin
  *
  * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace Plugin\CategoryContent\Event;

use Eccube\Application;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Plugin\CategoryContent\Entity\CategoryContent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CategoryContentEvent.
 */
class Event
{
    /**
     * プラグインが追加するフォーム名.
     */
    const CATEGORY_CONTENT_TEXTAREA_NAME = 'plg_category_content';

    /**
     * @var \Eccube\Application
     */
    private $app;

    const CATEGORY_CONTENT_TAG = '<!--# category-content-plugin-tag #-->';

    /**
     * CategoryContentEvent constructor.
     *
     * @param object $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 商品一覧画面にカテゴリコンテンツを表示する.
     *
     * @param TemplateEvent $event
     */
    public function onRenderProductList(TemplateEvent $event)
    {
        log_info('CategoryContent Product/list.twig start');
        $parameters = $event->getParameters();

        // カテゴリIDがない場合、レンダリングしない
        if (is_null($parameters['Category'])) {
            return;
        }

        // 登録がない、もしくは空で登録されている場合、レンダリングをしない
        $Category = $parameters['Category'];
        $CategoryContent = $this->app['category_content.repository.category_content']
            ->find($Category->getId());
        if (is_null($CategoryContent) || $CategoryContent->getContent() == '') {
            return;
        }

        // twigコードにカテゴリコンテンツを挿入
        $snipet = '<div class="row" style="margin-left: 0px;" >{{ CategoryContent.content|raw }}</div>';
        $sourceOrigin = $event->getSource();
        //find related product mark
        if (strpos($sourceOrigin, self::CATEGORY_CONTENT_TAG)) {
            log_info('Render category content with ', array('CATEGORY_CONTENT_TAG' => self::CATEGORY_CONTENT_TAG));
            $search = self::CATEGORY_CONTENT_TAG;
            $replace = $search.$snipet;
        } else {
            $search = '<div id="result_info_box"';
            $replace = $snipet.$search;
        }
        $source = str_replace($search, $replace, $sourceOrigin);
        $event->setSource($source);

        // twigパラメータにカテゴリコンテンツを追加
        $parameters['CategoryContent'] = $CategoryContent;
        $event->setParameters($parameters);
        log_info('CategoryContent Product/list.twig end');
    }

    /**
     * 管理画面：カテゴリ登録画面に, カテゴリコンテンツのフォームを追加する.
     *
     * @param EventArgs $event
     */
    public function onFormInitializeAdminProductCategory(EventArgs $event)
    {
        log_info('CategoryContent admin.product.category.index.initialize start');

        /* @var Category $TargetCategory */
        $TargetCategory = $event->getArgument('TargetCategory');
        $id = $TargetCategory->getId();

        $CategoryContent = null;

        if ($id) {
            // カテゴリ編集時は初期値を取得
            $CategoryContent = $this->app['category_content.repository.category_content']->find($id);
        }

        // カテゴリ新規登録またはコンテンツが未登録の場合
        if (is_null($CategoryContent)) {
            $CategoryContent = new CategoryContent();
        }

        // フォームの追加
        /** @var FormInterface $builder */
        $builder = $event->getArgument('builder');
        $builder->add(
            self::CATEGORY_CONTENT_TEXTAREA_NAME,
            'textarea',
            array(
                'required' => false,
                'label' => false,
                'mapped' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->app['config']['category_text_area_len'],
                    )),
                ),
                'attr' => array(
                    'maxlength' => $this->app['config']['category_text_area_len'],
                    'placeholder' => $this->app->trans('admin.plugin.category.content'),
                ),
            )
        );

        // 初期値を設定
        $builder->get(self::CATEGORY_CONTENT_TEXTAREA_NAME)->setData($CategoryContent->getContent());
        log_info('CategoryContent admin.product.category.index.initialize end');
    }

    /**
     * 管理画面：カテゴリ登録画面で、登録処理を行う.
     *
     * @param EventArgs $event
     */
    public function onAdminProductCategoryEditComplete(EventArgs $event)
    {
        log_info('CategoryContent admin.product.category.index.complete start');
        /** @var Application $app */
        $app = $this->app;
        /* @var Category $TargetCategory */
        $TargetCategory = $event->getArgument('TargetCategory');
        /** @var FormInterface $form */
        $form = $event->getArgument('form');

        // 現在のエンティティを取得
        $id = $TargetCategory->getId();
        $CategoryContent = $app['category_content.repository.category_content']->find($id);
        if (is_null($CategoryContent)) {
            $CategoryContent = new CategoryContent();
        }

        // エンティティを更新
        $CategoryContent
            ->setId($id)
            ->setContent($form[self::CATEGORY_CONTENT_TEXTAREA_NAME]->getData());

        // DB更新
        $app['orm.em']->persist($CategoryContent);
        $app['orm.em']->flush($CategoryContent);

        log_info('Category Content save successful !', array('category id' => $id));
        log_info('CategoryContent admin.product.category.index.complete end');
    }
}
