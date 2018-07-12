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

namespace Plugin\CategoryContent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Eccube\Entity\Category;
use Eccube\Event\TemplateEvent;

/**
 * Class CategoryContentEvent.
 */
class CategoryContentEvent implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => ['onFormPreSubmit', 10],
            '@admin/Product/category.twig' => ['onTemplateAdminProductCategory', 10],
            'Product/list.twig' => ['onTemplateProductList', 10],
        ];
    }

    /**
     * On pre submit
     *
     * @param FormEvent $event
     */
    public function onFormPreSubmit(FormEvent $event)
    {
        /** @var Category $Category */
        $Category = $event->getForm()->getData();

        if (!$Category instanceof Category || !$Category->getId()) {
            return;
        }

        $submitData = $event->getData();
        if (!isset($submitData['content'])) {
            $submitData['content'] = $Category->getContent();
        }
        if (!isset($submitData['name'])) {
            $submitData['name'] = $Category->getName();
        }

        $event->setData($submitData);
    }

    /**
     * Append JS to add edit content button
     *
     * @param TemplateEvent $templateEvent
     */
    public function onTemplateAdminProductCategory(TemplateEvent $templateEvent)
    {
        $templateEvent->addSnippet('@CategoryContent/admin/category.twig');
    }

    /**
     * Append JS to display category content
     *
     * @param TemplateEvent $templateEvent
     */
    public function onTemplateProductList(TemplateEvent $templateEvent)
    {
        $templateEvent->addSnippet('@CategoryContent/default/category_content.twig');
    }
}
