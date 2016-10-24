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

use Eccube\Plugin\AbstractPluginManager;
use Plugin\CategoryContent\Entity\CategoryContent;

/**
 * Class PluginManager.
 */
class PluginManager extends AbstractPluginManager
{
    /**
     * @var string コピー先リソースディレクトリ
     */
    private $target;

    /**
     * PluginManager constructor.
     */
    public function __construct()
    {
        // コピー先のディレクトリ
        $this->target = __DIR__.'/../../../html/plugin/categorycontent';
    }
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
        $em = $app['orm.em'];
        $em->getConnection()->beginTransaction();
        try {
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
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
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
    }
}
