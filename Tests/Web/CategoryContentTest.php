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

namespace Plugin\CateogryContent\Tests\Web;

use Eccube\Common\Constant;
use Eccube\Entity\Member;
use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryContentTest extends AbstractWebTestCase
{
    /**
     * カテゴリコンテンツの登録テスト
     *
     * - 管理画面＞カテゴリ登録画面を表示し、カテゴリコンテンツを登録する
     * - フロント＞商品一覧で表示されることを確認する
     */
    public function testRegisterCategoryContent()
    {
        if (version_compare(Constant::VERSION, '3.0.9', '<')) {
            $this->markTestSkipped();

            return;
        }

        // 管理画面へログイン
        $Member = $this->createMember();
        $client = $this->loginTo($Member);

        // カテゴリ登録画面へアクセス
        $crawler = $client->request('GET', $this->app->url('admin_product_category'));
        $this->assertTrue($client->getResponse()->isSuccessful());

        // コンテンツの登録エリアが表示されていることを確認する
        $html = $crawler->filter('.extra-form')->html();
        $this->assertContains('admin_category_plg_category_content', $html);

        // フォームに値をセットし、登録
        $form = $crawler->selectButton('カテゴリ作成')->form(
            array(
                'admin_category[name]' => 'テストカテゴリ',
                'admin_category[plg_category_content]' => 'テストコンテンツ',
            )
        );
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertContains('カテゴリを保存しました', $crawler->html());

        // 登録されたカテゴリIDを取得
        $item = $crawler->filter('#sortable_list_box__list')
            ->children()
            ->first()
            ->attr('id');
        // $item: sortable_list__item--xx
        $array = explode('--', $item);
        $category_id = $array[1];

        // 商品一覧画面にアクセスし、登録したカテゴリコンテンツが表示されていることを確認する
        $crawler = $client->request('GET', $this->app->url('product_list', array('category_id' => $category_id)));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('テストコンテンツ', $crawler->html());

        // 他のカテゴリには表示されないことを確認する
        $crawler = $client->request('GET', $this->app->url('product_list'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertNotContains('テストコンテンツ', $crawler->html());
    }

    /**
     * Member オブジェクトを生成して返す.
     *
     * @param string $username . null の場合は, ランダムなユーザーIDが生成される.
     * @return \Eccube\Entity\Member
     */
    public function createMember($username = null)
    {
        $faker = $this->getFaker();
        $Member = new Member();
        if (is_null($username)) {
            $username = $faker->word;
        }
        $Work = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Work')->find(1);
        $Authority = $this->app['eccube.repository.master.authority']->find(0);
        $Creator = $this->app['eccube.repository.member']->find(2);
        $salt = $this->app['eccube.repository.member']->createSalt(5);

        $Member
            ->setPassword('password')
            ->setLoginId($username)
            ->setName($username)
            ->setSalt($salt)
            ->setWork($Work)
            ->setAuthority($Authority)
            ->setCreator($Creator);
        $password = $this->app['eccube.repository.member']->encryptPassword($Member);
        $Member->setPassword($password);
        $this->app['eccube.repository.member']->save($Member);

        return $Member;
    }

    /**
     * User をログインさせてHttpKernel\Client を返す.
     *
     * @param UserInterface $User ログインさせる User
     * @return \Symfony\Component\HttpKernel\Client
     */
    public function loginTo(UserInterface $User)
    {
        $firewall = 'admin';
        $role = array('ROLE_ADMIN');
        if ($User instanceof \Eccube\Entity\Customer) {
            $firewall = 'customer';
            $role = array('ROLE_USER');
        }
        $token = new UsernamePasswordToken($User, null, $firewall, $role);

        $this->app['security.token_storage']->setToken($token);
        $this->app['session']->set('_security_'.$firewall, serialize($token));
        $this->app['session']->save();

        $cookie = new Cookie($this->app['session']->getName(), $this->app['session']->getId());
        $this->client->getCookieJar()->set($cookie);

        return $this->client;
    }
}
