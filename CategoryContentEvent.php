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

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Category;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CategoryContentEvent
{
    /**
     * プラグインが追加するフォーム名
     */
    const CATEGORY_CONTENT_TEXTAREA_NAME = 'plg_category_content';

    /**
     * @var \Eccube\Application
     */
    private $app;

    /**
     * v3.0.0 - 3.0.8 向けのイベントを処理するインスタンス
     * @var CategoryContentLegacyEvent
     */
    private $legacy_event;

    public function __construct($app)
    {
        $this->app = $app;
        $this->legacy_event = new CategoryContentLegacyEvent($app);
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

    public function onFormInitializeAdminProductCategory(EventArgs $event)
    {
        /** @var Category $target_category */
        $target_category = $event->getArgument('TargetCategory');

        // 編集中のターゲットIDが無い場合は、フォームを表示させない
        $id = $target_category->getId();
        if (is_null($id)) {
            return;
        }

        // 初期値の取得
        $CategoryContent = $this->app['category_content.repository.category_content']->find($id);
        if (is_null($CategoryContent)) {
            $CategoryContent = new \Plugin\CategoryContent\Entity\CategoryContent();
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
                'attr' => array(
                    'placeholder' => 'コンテンツを入力してください(HTMLタグ使用可)',
                )
            )
        );

        // 初期値を設定
        $builder->get('plg_content')->setData($CategoryContent->getContent());
    }

    public function onAdminProductCategoryEditComplete(EventArgs $event)
    {
        if (!$this->supportNewHookPoint()) return;

        /** @var Application $app */
        $app = $this->app;
        /** @var Category $target_category */
        $target_category = $event['TargetCategory'];
        /** @var FormInterface $form */
        $form = $event['form'];

        // 現在のエンティティを取得
        $id = $target_category->getId();
        $CategoryContent = $app['category_content.repository.category_content']->find($id);
        if (is_null($CategoryContent)) {
            $CategoryContent = new \Plugin\CategoryContent\Entity\CategoryContent();
        }

        // エンティティを更新
        $CategoryContent
            ->setId($id)
            ->setContent($form[self::CATEGORY_CONTENT_TEXTAREA_NAME]->getData());

        // DB更新
        $app['orm.em']->persist($CategoryContent);
        $app['orm.em']->flush();
    }

#region v3.0.0 - 3.0.8 用のイベント
    /**
     * for v3.0.0 - 3.0.8
     * @deprecated for since v3.0.0, to be removed in 3.1.
     */
    public function onRenderProductListBefore(FilterResponseEvent $event)
    {
        if ($this->supportNewHookPoint()) return;

        $this->legacy_event->onRenderProductListBefore($event);
    }

    /**
     * for v3.0.0 - 3.0.8
     * @deprecated for since v3.0.0, to be removed in 3.1.
     */
    public function onRenderAdminProductCategoryEditBefore(FilterResponseEvent $event)
    {
        if ($this->supportNewHookPoint()) return;

        $this->legacy_event->onRenderAdminProductCategoryEditBefore($event);
    }

    /**
     * for v3.0.0 - 3.0.8
     * @deprecated for since v3.0.0, to be removed in 3.1.
     */
    public function onAdminProductCategoryEditAfter()
    {
        if ($this->supportNewHookPoint()) return;

        $this->legacy_event->onAdminProductCategoryEditAfter();
    }
# endregion

    /**
     * @return bool v3.0.9以降のフックポイントに対応しているか？
     */
    private function supportNewHookPoint()
    {
        return version_compare('3.0.9', Constant::VERSION, '<=');
    }
}
