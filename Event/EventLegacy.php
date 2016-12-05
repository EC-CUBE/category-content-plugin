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

    const CATEGORY_CONTENT_TAG = '<!--# category-content-plugin-tag #-->';
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
        if (!$id) {
            return;
        }

        $CategoryContent = $app['eccube.plugin.category_content.repository.category_content']->find($id);

        // 登録がない、もしくは空で登録されている場合、レンダリングを変更しない
        if (!$CategoryContent || !$CategoryContent->getContent()) {
            log_info('CategoryContent eccube.event.render.product_list.before  not content end');

            return;
        }

        // twigから挿入するhtmlを生成する
        $snipet = $this->app->renderView(
            'CategoryContent/Resource/template/default/category_content.twig',
            array('PluginCategoryContent' => $CategoryContent)
        );

        // htmlの挿入処理
        $html = $response->getContent();
        $search = self::CATEGORY_CONTENT_TAG;
        if (strpos($html, $search)) {
            // タグの位置に挿入する場合
            log_info('Render category content with ', array('CATEGORY_CONTENT_TAG' => $search));
            $replace = $search.$snipet;
            $newHtml = str_replace($search, $replace, $html);
            $response->setContent($newHtml);
        } else {
            // Elementを探して挿入する場合
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
            $dom->encoding = 'UTF-8';
            $dom->formatOutput = true;

            // 基準となるElementを取得
            $navElement = $dom->getElementById('topicpath');
            if (!$navElement instanceof \DOMElement) {
                log_info('CategoryContent eccube.event.render.product_list.before  not have dom end');

                return;
            }

            // 挿入するNodeを生成
            $template = $dom->createDocumentFragment();
            $template->appendXML(htmlspecialchars($snipet));
            $node = $dom->importNode($template, true);

            // 基準となるElementの直後にNodeを挿入し、Responsを書き換え
            $navElement->parentNode->insertBefore($node, $navElement->nextSibling);
            $newHtml = html_entity_decode($dom->saveHTML(), ENT_NOQUOTES, 'UTF-8');
            $response->setContent($newHtml);
        }
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
            $CategoryContent = $app['eccube.plugin.category_content.repository.category_content']->find($id);
        }

        if (!$CategoryContent) {
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

        $CategoryContent = $app['eccube.plugin.category_content.repository.category_content']
            ->find($id);

        if (!$CategoryContent) {
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
