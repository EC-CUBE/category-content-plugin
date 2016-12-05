<?php
/*
  * This file is part of the CategoryContent plugin
  *
  * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace Plugin\CategoryContent;

use Eccube\Application;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Plugin\CategoryContent\Util\Version;

/**
 * Class CategoryContentEvent.
 */
class Event
{
    /**
     * @var \Eccube\Application
     */
    private $app;

    /**
     * CategoryContentEvent constructor.
     *
     * @param Application $app
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
        $this->app['eccube.plugin.categorycontent.event']->onRenderProductList($event);
    }

    /**
     * 管理画面：カテゴリ登録画面に, カテゴリコンテンツのフォームを追加する.
     *
     * @param EventArgs $event
     */
    public function onAdminProductCategoryFormInitialize(EventArgs $event)
    {
        $this->app['eccube.plugin.categorycontent.event']->onAdminProductCategoryFormInitialize($event);
    }

    /**
     * 管理画面：カテゴリ登録画面で、登録処理を行う.
     *
     * @param EventArgs $event
     */
    public function onAdminProductCategoryEditComplete(EventArgs $event)
    {
        $this->app['eccube.plugin.categorycontent.event']->onAdminProductCategoryEditComplete($event);
    }

//region v3.0.0 - 3.0.8 用のイベント
    /**
     * onRenderProductListBefore.
     *
     * for v3.0.0 - 3.0.8
     *
     * @param FilterResponseEvent $event
     *
     * @deprecated for since v3.0.0, to be removed in 3.1
     */
    public function onRenderProductListBefore(FilterResponseEvent $event)
    {
        if (Version::isSupportNewHookPoint()) {
            return;
        }
        $this->app['eccube.plugin.categorycontent.event_legacy']->onRenderProductListBefore($event);
    }

    /**
     * onRenderAdminProductCategoryEditBefore.
     *
     * for v3.0.0 - 3.0.8
     *
     * @param FilterResponseEvent $event
     *
     * @deprecated for since v3.0.0, to be removed in 3.1
     */
    public function onRenderAdminProductCategoryEditBefore(FilterResponseEvent $event)
    {
        if (Version::isSupportNewHookPoint()) {
            return;
        }
        $this->app['eccube.plugin.categorycontent.event_legacy']->onRenderAdminProductCategoryEditBefore($event);
    }

    /**
     * onAdminProductCategoryEditAfter.
     *
     * for v3.0.0 - 3.0.8
     *
     * @deprecated for since v3.0.0, to be removed in 3.1
     */
    public function onAdminProductCategoryEditAfter()
    {
        if (Version::isSupportNewHookPoint()) {
            return;
        }
        $this->app['eccube.plugin.categorycontent.event_legacy']->onAdminProductCategoryEditAfter();
    }
// endregion
}
