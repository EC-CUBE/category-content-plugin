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

namespace Plugin\CategoryContent\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CategoryContentServiceProvider
 * @package Plugin\CategoryContent\ServiceProvider
 */
class CategoryContentServiceProvider implements ServiceProviderInterface
{
    /**
     * register
     *
     * @param BaseApplication $app
     */
    public function register(BaseApplication $app)
    {
        // Form/Extension
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Plugin\CategoryContent\Form\Extension\CategoryContentExtension($app['config'], $app);

            return $extensions;
        }));

        //Repository
        $app['category_content.repository.category_content'] = $app->share(function () use ($app) {

            return $app['orm.em']->getRepository('Plugin\CategoryContent\Entity\CategoryContent');
        });

        // メッセージ登録
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new YamlFileLoader());

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
     * boot
     *
     * @param BaseApplication $app
     */
    public function boot(BaseApplication $app)
    {
    }
}

