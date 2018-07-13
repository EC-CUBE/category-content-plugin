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

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Repository\CategoryRepository;
use Eccube\Entity\Category;

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
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Eccube\Entity\Category
     */
    protected $Category;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->categoryRepository = $this->container->get(CategoryRepository::class);
        $this->Category = $this->categoryRepository->find(CATEGORY_ID_3);
        $this->addCategoryContent(CATEGORY_ID_3, CATEGORY_CONTENT);
    }

    /**
     * カテゴリ画面のルーティング.
     */
    public function testRenderCategory()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_product_category')
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
            $this->generateUrl('admin_product_category_show', ['parent_id' => $this->Category->getParent()->getId()])
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
            $this->generateUrl('admin_product_category'),
            [
                'category_'.CATEGORY_ID_1 => [
                    '_token' => 'dummy',
                    'name' => CATEGORY_NAME,
                    'category_content_content' => CATEGORY_CONTENT,
                ],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_product_category')));

        $categoryName = $this->categoryRepository->find(CATEGORY_ID_1)->getCategoryContentContent();

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
        /** @var Category $Category */
        $Category = $this->categoryRepository->find($id);
        $Category->setCategoryContentContent($content);
        $this->categoryRepository->save($Category);
    }
}
