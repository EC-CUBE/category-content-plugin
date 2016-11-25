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
use Plugin\CategoryContent\Entity\CategoryContent;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Hook point implementation for v3.0.0 - 3.0.8.
 */
class EventLegacy
{
    /**
     * @var \Eccube\Application
     */
    private $app;

    /**
     * CategoryContentLegacyEvent constructor.
     *
     * @param object $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * onRenderProductListBefore.
     *
     * @param FilterResponseEvent $event
     */
    public function onRenderProductListBefore(FilterResponseEvent $event)
    {
        log_info('CategoryContent eccube.event.render.product_list.before start');
        $app = $this->app;

        $request = $event->getRequest();
        $response = $event->getResponse();

        $id = $request->query->get('category_id');

        // category_idがない場合、レンダリングを変更しない
        if (is_null($id)) {
            return;
        }

        $CategoryContent = $app['category_content.repository.category_content']->find($id);

        // 登録がない、もしくは空で登録されている場合、レンダリングを変更しない
        if (!$CategoryContent || !$CategoryContent->getContent()) {
            log_info('CategoryContent eccube.event.render.product_list.before  not content end');

            return;
        }

        // 書き換えhtmlの初期化
        $snipet = '<div class="row" style="margin-left: 0px;" >'.$CategoryContent->getContent().'</div>';
        $sourceOrigin = $response->getContent();
        //find related product mark
        if (strpos($sourceOrigin, self::CATEGORY_CONTENT_TAG)) {
            log_info('Render category content with ', array('CATEGORY_CONTENT_TAG' => self::CATEGORY_CONTENT_TAG));
            $search = self::CATEGORY_CONTENT_TAG;
            $replace = $search.$snipet;
        } else {
            $search = '<!-- ▲topicpath▲ -->';
            $replace = $search.$snipet;
        }
        $source = str_replace($search, $replace, $sourceOrigin);
        $response->setContent($source);
        $event->setResponse($response);
        log_info('CategoryContent eccube.event.render.product_list.before end');
    }

    /**
     * onRenderAdminProductCategoryEditBefore.
     *
     * @param FilterResponseEvent $event
     */
    public function onRenderAdminProductCategoryEditBefore(FilterResponseEvent $event)
    {
        log_info('CategoryContent eccube.event.render.admin_product_category_edit.before start');
        $app = $this->app;
        $request = $event->getRequest();
        $response = $event->getResponse();

        $id = $request->attributes->get('id');

        $CategoryContent = null;

        if ($id) {
            $CategoryContent = $app['category_content.repository.category_content']->find($id);
        }

        if (is_null($CategoryContent)) {
            $CategoryContent = new CategoryContent();
        }

        // DomCrawlerにHTMLを食わせる
        $html = $response->getContent();
        $crawler = new Crawler($html);

        $form = $app['form.factory']
            ->createBuilder('admin_category')
            ->getForm();

        $form['content']->setData($CategoryContent->getContent());
        $form->handleRequest($request);

        $twig = $app->renderView(
            'CategoryContent/Resource/template/admin/category.twig',
            array('form' => $form->createView())
        );

        $oldCrawler = $crawler
            ->filter('form')
            ->first();

        // DomCrawlerからHTMLを吐き出す
        $html = $crawler->html();
        $oldHtml = '';
        $newHtml = '';
        if (count($oldCrawler) > 0) {
            $oldHtml = $oldCrawler->html();
            $newHtml = $oldHtml.$twig;
        }

        $html = str_replace($oldHtml, $newHtml, $html);

        $response->setContent($html);
        $event->setResponse($response);
        log_info('CategoryContent eccube.event.render.admin_product_category_edit.before end');
    }

    /**
     * onAdminProductCategoryEditAfter.
     */
    public function onAdminProductCategoryEditAfter()
    {
        log_info('CategoryContent eccube.event.controller.admin_product_category_edit.after start');
        $app = $this->app;

        if ('POST' !== $app['request']->getMethod()) {
            log_info('CategoryContent eccube.event.controller.admin_product_category_edit.after not post end');

            return;
        }

        $id = $app['request']->attributes->get('id');

        $form = $app['form.factory']
            ->createBuilder('admin_category')
            ->getForm();

        $CategoryContent = $app['category_content.repository.category_content']
            ->find($id);

        if (is_null($CategoryContent)) {
            $CategoryContent = new CategoryContent();
        }

        $form->handleRequest($app['request']);

        if ($form->isValid()) {
            $CategoryContent
                ->setId($id)
                ->setContent($form['content']->getData());

            $app['orm.em']->persist($CategoryContent);
            $app['orm.em']->flush($CategoryContent);
        }
        log_info('CategoryContent eccube.event.controller.admin_product_category_edit.after end');
    }
}
