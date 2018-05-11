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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Plugin\CategoryContent\Event\Event;

/**
 * Class CategoryContentEvent.
 */
class EventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Event
     */
    private $event;

    /**
     * EventSubscriber constructor.
     *
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
//            'Product/list.twig' => ['onRenderProductList', 10],
            'admin.product.category.index.initialize' => ['onAdminProductCategoryIndexInitialize', 10],
//            'admin.product.category.index.complete' => ['onAdminProductCategoryEditComplete', 10],
        ];
    }


    /**
     * 商品一覧画面にカテゴリコンテンツを表示する.
     *
     * @param TemplateEvent $event
     */
    public function onRenderProductList(TemplateEvent $event)
    {
        $this->event->onRenderProductList($event);
    }

    /**
     * 管理画面：カテゴリ登録画面に, カテゴリコンテンツのフォームを追加する.
     *
     * @param EventArgs $event
     */
    public function onAdminProductCategoryIndexInitialize(EventArgs $event)
    {
        $this->event->onAdminProductCategoryIndexInitialize($event);
    }

    /**
     * 管理画面：カテゴリ登録画面で、登録処理を行う.
     *
     * @param EventArgs $event
     */
    public function onAdminProductCategoryEditComplete(EventArgs $event)
    {
        $this->event->onAdminProductCategoryEditComplete($event);
    }
}
