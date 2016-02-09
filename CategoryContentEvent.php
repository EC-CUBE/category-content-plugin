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

use Eccube\Common\Constant;
use Eccube\Event\TemplateEvent;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CategoryContentEvent
{
    /**
     * @var \Eccube\Application
     */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function onRenderProductList(TemplateEvent $event)
    {
        if (!$this->supportNewHookPoint()) return;

        $id = $event->getParameters()['Category']['id'];

        // category_idがない場合、レンダリングを変更しない
        if (is_null($id)) {
            return;
        }

        $CategoryContent = $this->app['category_content.repository.category_content']
            ->find($id);

        // 登録がない、もしくは空で登録されている場合、レンダリングを変更しない
        if (is_null($CategoryContent) || $CategoryContent->getContent() == '') {
            return;
        }

        // 挿入対象を取得
        $dom = $this->Twig2DOMDocument($event->getSource());
        $navElement = $dom->getElementById('page_navi_top');
        if (!$navElement instanceof \DOMElement) {
            return;
        }
        // カテゴリコンテンツを挿入
        $template = $dom->createDocumentFragment();
        $template->appendXML($CategoryContent->getContent());
        $node = $dom->importNode($template, true);
        $navElement->insertBefore($node);

        $event->setSource($this->DOMDocument2Twig($dom));
    }

    /**
     * Twig -> DOMDocument
     * @param string $twig_code
     * @return \DOMDocument
     */
    private function Twig2DOMDocument($twig_code)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        // Twigコード部分のみを取得できるように<div id="_twigcode">で囲っておく
        $source = mb_convert_encoding('<div id="_twigcode">' . $twig_code . '</div>', 'HTML-ENTITIES', "UTF-8");
        $dom->loadHTML($source);

        return $dom;
    }

    /**
     * DOMDocument -> Twig
     * @param \DOMDocument $dom
     * @return string
     */
    private function DOMDocument2Twig(\DOMDocument $dom)
    {
        // saveHTMLだとタグ中のTwigコードが文字化けするため、saveXMLで取得する
        $str = $dom->saveXML($dom->getElementById("_twigcode"));
        // saveXMLで取得した文字列が一部UTF-8になっているため、HTML-ENTITIESに再変換して統一する
        $str = mb_convert_encoding($str, 'HTML-ENTITIES', "UTF-8");
        // HTML-ENTITIESからUTF-8への変換
        $str = mb_convert_encoding($str, 'UTF-8', "HTML-ENTITIES");
        // <div id="_twigcode">タグを削除
        $twig_code = preg_replace('@^<div id="_twigcode"[^>]*>|</div>$@', '', $str);

        return $twig_code;
    }

    public function onRenderProductListBefore(FilterResponseEvent $event)
    {
        if ($this->supportNewHookPoint()) return;

        $app = $this->app;

        $request = $event->getRequest();
        $response = $event->getResponse();

        $id = $request->query->get('category_id');

        // category_idがない場合、レンダリングを変更しない
        if (is_null($id)) {
            return;
        }

        $CategoryContent = $app['category_content.repository.category_content']
            ->find($id);

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

        $newHtml = html_entity_decode($dom->saveHTML(), ENT_NOQUOTES, 'UTF-8');
        $response->setContent($newHtml);
        $event->setResponse($response);
    }

    public function onRenderAdminProductCategoryEditBefore(FilterResponseEvent $event)
    {
        $app = $this->app;
        $request = $event->getRequest();
        $response = $event->getResponse();

        $id = $request->attributes->get('id');

        $CategoryContent = null;

        if ($id) {
            $CategoryContent = $app['category_content.repository.category_content']
                ->find($id);
        }

        if (is_null($CategoryContent)) {
            $CategoryContent = new \Plugin\CategoryContent\Entity\CategoryContent();
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

        if ('POST' !== $app['request']->getMethod()) {
            return;
        }

        $id = $app['request']->attributes->get('id');

        $form = $app['form.factory']
            ->createBuilder('admin_category')
            ->getForm();

        $CategoryContent = $app['category_content.repository.category_content']
            ->find($id);

        if (is_null($CategoryContent)) {
            $CategoryContent = new \Plugin\CategoryContent\Entity\CategoryContent();
        }

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);

            if ($form->isValid()) {

                $CategoryContent
                    ->setId($id)
                    ->setContent($form['content']->getData());

                $app['orm.em']->persist($CategoryContent);
                $app['orm.em']->flush();
            }
        }
    }

    /**
     * @return bool v3.0.9以降のフックポイントに対応しているか？
     */
    private function supportNewHookPoint()
    {
        return version_compare('3.0.9', Constant::VERSION, '<=');
    }
}
