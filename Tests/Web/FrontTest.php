<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CategoryContent\Tests\Web;

use Eccube\Tests\Web\AbstractWebTestCase;
use Eccube\Repository\CategoryRepository;
use Eccube\Entity\Category;

if (!defined('CATEGORY_CONTENT')) {
    define('CATEGORY_CONTENT', 'テストカテゴリコンテンツ');
}
const CATEGORY_ID = 3;

/**
 * Class FrontTest.
 */
class FrontTest extends AbstractWebTestCase
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Setup.
     */
    public function setUp()
    {
        $this->markTestSkipped('Skip due to manual modify template');
        parent::setUp();
        $this->categoryRepository = $this->container->get(CategoryRepository::class);
        $this->addCategoryContent(CATEGORY_ID, CATEGORY_CONTENT);
    }

    /**
     * カテゴリ画面のルーティング.
     */
    public function testRoutingProduct()
    {
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('product_list', ['category_id' => CATEGORY_ID])
        );
        $this->assertContains(CATEGORY_CONTENT, $crawler->html());
    }

    /**
     * addCategoryContent.
     *
     * @param $id
     * @param $content
     */
    private function addCategoryContent($id, $content)
    {
        /** @var Category $Category */
        $Category = $this->categoryRepository->find($id);
        $Category->setContent($content);
        $this->categoryRepository->save($Category);
    }
}
