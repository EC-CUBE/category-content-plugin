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

namespace Plugin\CategoryContent;

use Eccube\Event\RenderEvent;
use Eccube\Event\ShoppingEvent;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CategoryContent
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function onRenderProductListBefore(FilterResponseEvent $event)
    {
        $app = $this->app;
        $request = $event->getRequest();
        $response = $event->getResponse();

        $id = $request->query->get('category_id');

        // category_idがない場合、レンダリングを変更しない
        if (is_null($id)) {
            return;
        }

        $CategoryContent = $app['category_content.repository.category_content']
            ->findOneBy(array('category_id' => $id));

        // 登録がない、もしくは空で登録されている場合、レンダリングを変更しない
        if (is_null($CategoryContent) || $CategoryContent->getContent() == '') {
            return;
        }

        // 書き換えhtmlの初期化
        $html = $response->getContent();
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $dom->encoding = "UTF-8";
        $dom->formatOutput = true;

        // 挿入対象を取得
        $navElement = $dom->getElementById('page_navi_top');
        if (!$navElement instanceof \DOMElement) {
            return;
        }

        $template = $dom->createDocumentFragment();
        $template->appendXML($CategoryContent->getContent());

        $node = $dom->importNode($template, true);
        $navElement->insertBefore($node);

        $response->setContent($dom->saveHTML());
        $event->setResponse($response);
    }

    public function onRenderAdminProductCategoryEditBefore(FilterResponseEvent $event)
    {
        $app = $this->app;
        $request = $event->getRequest();
        $response = $event->getResponse();
        $id = $request->attributes->get('id');

        // DomCrawlerにHTMLを食わせる
        $html = $response->getContent();
        $crawler = new Crawler($html);

        $CategoryContent = $app['category_content.repository.category_content']->find($id);
        if (is_null($CategoryContent)) {
            $CategoryContent = new \Plugin\CategoryContent\Entity\CategoryContent();
        }

        $form = $app['form.factory']
            ->createBuilder('admin_category')
            ->getForm();
        $form->get('content')->setData($CategoryContent->getContent());
        $form->handleRequest($request);

        $twig = $app->renderView(
            'CategoryContent/Resource/template/Admin/category.twig',
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
            $newHtml = $oldHtml . $twig;
        }

        $html = str_replace($oldHtml, $newHtml, $html);

        $response->setContent($html);
        $event->setResponse($response);
    }

    public function onAdminProductCategoryEditAfter()
    {
        $app = $this->app;
        $id = $app['request']->attributes->get('id');

        $form = $app['form.factory']
            ->createBuilder('admin_category')
            ->getForm();

        $CategoryContent = $app['category_content.repository.category_content']->find($id);
        if (is_null($CategoryContent)) {
            $CategoryContent = new \Plugin\CategoryContent\Entity\CategoryContent();
        }
        $form->get('content')->setData($CategoryContent->getContent());

        $form->handleRequest($app['request']);

        if ('POST' === $app['request']->getMethod()) {
            if ($form->isValid()) {
                $content = $form->get('content')->getData();

                $Category = $app['eccube.repository.category']->find($id);

                $CategoryContent
                    ->setCategoryId($Category->getId())
                    ->setCategory($Category)
                    ->setContent($content);

                $app['orm.em']->persist($CategoryContent);
                $app['orm.em']->flush();
            }
        }
    }

}