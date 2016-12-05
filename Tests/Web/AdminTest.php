<?php
/*
  * This file is part of the CategoryContent plugin
  *
  * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace Plugin\CategoryContent\Tests\Web;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Plugin\CategoryContent\Entity\CategoryContent;

if (!defined('CATEGORY_CONTENT')) {
    define('CATEGORY_CONTENT', 'テストカテゴリコンテンツ');
}
const CATEGORY_NAME = 'テストカテゴリ';
const CATEGORY_ID_1 = 1;
const CATEGORY_ID_2 = 2;
const CATEGORY_ID_3 = 3;

/**
 * Class AdminTest.
 */
class AdminTest extends AbstractAdminWebTestCase
{
    /**
     * Setup.
     */
    public function setUp()
    {
        parent::setUp();
        $this->addCategoryContent(CATEGORY_ID_3, CATEGORY_CONTENT);
    }

    /**
     * カテゴリ画面のルーティング.
     */
    public function testRenderCategory()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_product_category')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * testRoutingCategoryEdit.
     */
    public function testRenderCategoryEdit()
    {
        $crawler = $this->client->request(
            'GET',
            $this->app->url('admin_product_category_edit', array('id' => CATEGORY_ID_3))
        );
        $this->assertContains(CATEGORY_CONTENT, $crawler->html());
    }

    /**
     * testRoutingCategoryEdit.
     */
    public function testCategoryEdit()
    {
        $this->client->request(
            'POST',
            $this->app->url('admin_product_category_edit', array('id' => CATEGORY_ID_1)),
            array(
                'admin_category' => array(
                '_token' => 'dummy',
                'name' => CATEGORY_NAME,
                'plg_category_content' => CATEGORY_CONTENT,
            ), )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product_category')));

        $categoryName = $this->app['eccube.plugin.category_content.repository.category_content']->find(CATEGORY_ID_1)->getContent();

        $this->expected = CATEGORY_CONTENT;
        $this->actual = $categoryName;
        $this->verify();
    }

    /**
     * addCategoryContent.
     *
     * @param $id
     * @param $content
     */
    private function addCategoryContent($id, $content)
    {
        $CategoryContent = new CategoryContent();
        $CategoryContent
            ->setId($id)
            ->setContent($content);
        $this->app['orm.em']->persist($CategoryContent);
        $this->app['orm.em']->flush($CategoryContent);
    }
}
