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
use Eccube\Plugin\AbstractPluginManager;
use Plugin\CategoryContent\Entity\CategoryContent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PluginManager.
 */
class PluginManager extends AbstractPluginManager
{
    /**
     * プラグイン有効時の処理.
     *
     * @param array $config
     * @param Application|null $app
     * @param ContainerInterface|null $container
     * @return void
     */
    public function enable($config = [], Application $app = null, ContainerInterface $container = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');

        // serviceで定義している情報が取得できないため、直接呼び出す
        try {
            // EC-CUBE3.0.3対応
            $CategoryContent = $entityManager->getRepository(CategoryContent::class)->find(1);
        } catch (\Exception $e) {
            return null;
        }
        if (!$CategoryContent) {
            $CategoryContent = new CategoryContent();
            // IDは1固定
            $CategoryContent->setId(1);
            $entityManager->persist($CategoryContent);
            $entityManager->flush();
        }
    }
}
