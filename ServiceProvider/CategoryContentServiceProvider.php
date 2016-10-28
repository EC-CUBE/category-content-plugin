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
        // @deprecated for since v3.0.0, to be removed in 3.1.
        if (version_compare(Constant::VERSION, '3.0.9', '<')) {
            // Form/Extension
            $app['form.type.extensions'] = $app->share(
                $app->extend(
                    'form.type.extensions',
                    function ($extensions) {
                        $extensions[] = new CategoryContentExtension();

                        return $extensions;
                    }
                )
            );
        }
        //Repository
        $app['category_content.repository.category_content'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\CategoryContent\Entity\CategoryContent');
        });

        // メッセージ登録
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $file = __DIR__.'/../Resource/locale/message.'.$app['locale'].'.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }

            return $translator;
        }));

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
