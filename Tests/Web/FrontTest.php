<?php

namespace Plugin\CategoryContent\Tests\Web;

use Eccube\Tests\Web\AbstractWebTestCase;
use Plugin\CategoryContent\Entity\CategoryContent;

const CATEGORY_CONTENT = 'テストカテゴリコンテンツ';
const CATEGORY_ID = 3;

/**
 * Class FrontTest.
 */
class FrontTest extends AbstractWebTestCase
{
    /**
     * Setup.
     */
    public function setUp()
    {
        parent::setUp();
        $this->addCategoryContent(CATEGORY_ID, CATEGORY_CONTENT);
    }

    /**
     * カテゴリ画面のルーティング.
     */
    public function testRoutingProduct()
    {
        $crawler = $this->client->request(
            'GET',
            $this->app->url('product_list', array('category_id' => CATEGORY_ID))
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
        $CategoryContent = new CategoryContent();
        $CategoryContent
            ->setId($id)
            ->setContent($content);
        $this->app['orm.em']->persist($CategoryContent);
        $this->app['orm.em']->flush($CategoryContent);
    }
}
