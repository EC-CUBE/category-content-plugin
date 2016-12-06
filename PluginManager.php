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

use Eccube\Plugin\AbstractPluginManager;
use Plugin\CategoryContent\Entity\CategoryContent;

/**
 * Class PluginManager.
 */
class PluginManager extends AbstractPluginManager
{
    /**
     * プラグインインストール時の処理.
     *
     * @param array  $config
     * @param object $app
     *
     * @throws \Exception
     */
    public function install($config, $app)
    {
    }
    /**
     * プラグイン削除時の処理.
     *
     * @param array  $config
     * @param object $app
     */
    public function uninstall($config, $app)
    {
        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code'], 0);
    }

    /**
     * プラグイン有効時の処理.
     *
     * @param array  $config
     * @param object $app
     *
     * @throws \Exception
     */
    public function enable($config, $app)
    {
        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code']);
        $em = $app['orm.em'];

        // serviceで定義している情報が取得できないため、直接呼び出す
        try {
            // EC-CUBE3.0.3対応
            $CategoryContent = $em->getRepository('Plugin\CategoryContent\Entity\CategoryContent')->find(1);
        } catch (\Exception $e) {
            return null;
        }
        if (!$CategoryContent) {
            $CategoryContent = new CategoryContent();
            // IDは1固定
            $CategoryContent->setId(1);
            $em->persist($CategoryContent);
            $em->flush($CategoryContent);
        }
    }
    /**
     * プラグイン無効時の処理.
     *
     * @param array  $config
     * @param object $app
     */
    public function disable($config, $app)
    {
    }

    /**
     * @param array  $config
     * @param object $app
     */
    public function update($config, $app)
    {
        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code']);
    }
}
