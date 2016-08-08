<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CategoryContent\ServiceProvider;

use Eccube\Application;
use Eccube\Common\Constant;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class CategoryContentServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // @deprecated for since v3.0.0, to be removed in 3.1.
        if (version_compare(Constant::VERSION, '3.0.9', '<')) {
            // Form/Extension
            $app['form.type.extensions'] = $app->share(
                $app->extend(
                    'form.type.extensions',
                    function ($extensions) {
                        $extensions[] = new \Plugin\CategoryContent\Form\Extension\CategoryContentExtension();

                        return $extensions;
                    }
                )
            );
        }

        // Repository
        $app['category_content.repository.category_content'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\CategoryContent\Entity\CategoryContent');
        });

    }

    public function boot(BaseApplication $app)
    {
    }
}
