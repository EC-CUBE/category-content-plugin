<?php
/*
  * This file is part of the CategoryContent plugin
  *
  * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace Plugin\CategoryContent\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;
use Plugin\CategoryContent\Form\Extension\CategoryContentExtension;
use Plugin\CategoryContent\Event\Event;
use Plugin\CategoryContent\Event\EventLegacy;

// include log functions (for 3.0.0 - 3.0.11)
require_once __DIR__.'/../log.php';

/**
 * Class CategoryContentServiceProvider.
 */
class CategoryContentServiceProvider implements ServiceProviderInterface
{
    /**
     * register.
     *
     * @param BaseApplication $app
     */
    public function register(BaseApplication $app)
    {
        // イベントの追加
        $app['eccube.plugin.categorycontent.event'] = $app->share(function () use ($app) {
            return new Event($app);
        });
        $app['eccube.plugin.categorycontent.event_legacy'] = $app->share(function () use ($app) {
            return new EventLegacy($app);
        });

        // @deprecated for since v3.0.0, to be removed in 3.1.
        if (!method_exists('Eccube\Application', 'getInstance')) {
            // Form/Extension
            $app['form.type.extensions'] = $app->share(
                $app->extend(
                    'form.type.extensions',
                    function ($extensions) use ($app) {
                        $extensions[] = new CategoryContentExtension($app);

                        return $extensions;
                    }
                )
            );
        }

        //Repository
        $app['eccube.plugin.category_content.repository.category_content'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\CategoryContent\Entity\CategoryContent');
        });

        // メッセージ登録
        $file = __DIR__.'/../Resource/locale/message.'.$app['locale'].'.yml';
        $app['translator']->addResource('yaml', $file, $app['locale']);

        //Config
        $app['config'] = $app->share($app->extend('config', function ($config) {
            // Update constants
            $constantFile = __DIR__.'/../Resource/config/constant.yml';
            if (file_exists($constantFile)) {
                $constant = Yaml::parse(file_get_contents($constantFile));
                if (!empty($constant)) {
                    // Replace constants
                    $config = array_replace_recursive($config, $constant);
                }
            }

            return $config;
        }));

        // initialize logger (for 3.0.0 - 3.0.8)
        if (!method_exists('Eccube\Application', 'getInstance')) {
            eccube_log_init($app);
        }
    }

    /**
     * boot.
     *
     * @param BaseApplication $app
     */
    public function boot(BaseApplication $app)
    {
    }
}
