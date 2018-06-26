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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Eccube\Entity\Category;

/**
 * Class CategoryContentEvent.
 */
class EventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => ['onFormPreSubmit', 10]
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
}
