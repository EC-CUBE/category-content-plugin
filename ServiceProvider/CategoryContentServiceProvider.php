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

class CategoryContentServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // Form/Extension
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) {
            $extensions[] = new \Plugin\CategoryContent\Form\Extension\CategoryContentExtension();
            return $extensions;
        }));

        //Repository
        $app['category_content.repository.category_content'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\CategoryContent\Entity\CategoryContent');
        });

    }

    public function boot(BaseApplication $app)
    {
    }
}
