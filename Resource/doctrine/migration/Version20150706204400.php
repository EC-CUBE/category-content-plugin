<?php
/*
  * This file is part of the CategoryContent plugin
  *
  * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Application;
use Doctrine\ORM\EntityManager;
use Plugin\CategoryContent\Util\Version;

/**
 * Class Version20150706204400.
 */
class Version20150706204400 extends AbstractMigration
{
    /**
     * @var string table name
     */
    const NAME = 'plg_category_content';

    protected $entities = array(
        'Plugin\CategoryContent\Entity\CategoryContent',
    );

    /**
     * Setup data.
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (Version::isSupportGetInstanceFunction()) {
            $this->createCategoryContent($schema);
        } else {
            $this->createCategoryContentForOldVersion($schema);
        }
    }

    /**
     * Remove data.
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        if (Version::isSupportGetInstanceFunction()) {
            $app = Application::getInstance();
            $meta = $this->getMetadata($app['orm.em']);
            $tool = new SchemaTool($app['orm.em']);
            $schemaFromMetadata = $tool->getSchemaFromMetadata($meta);
            // テーブル削除
            foreach ($schemaFromMetadata->getTables() as $table) {
                if ($schema->hasTable($table->getName())) {
                    $schema->dropTable($table->getName());
                }
            }
            // シーケンス削除
            foreach ($schemaFromMetadata->getSequences() as $sequence) {
                if ($schema->hasSequence($sequence->getName())) {
                    $schema->dropSequence($sequence->getName());
                }
            }
        } else {
            // this down() migration is auto-generated, please modify it to your needs
            $schema->dropTable(self::NAME);
        }
    }

    /**
     * Create recommend table.
     *
     * @param Schema $schema
     *
     * @return bool
     */
    protected function createCategoryContent(Schema $schema)
    {
        if ($schema->hasTable(self::NAME)) {
            return true;
        }

        $app = Application::getInstance();
        $em = $app['orm.em'];
        $classes = array(
            $em->getClassMetadata('Plugin\CategoryContent\Entity\CategoryContent'),
        );
        $tool = new SchemaTool($em);
        $tool->createSchema($classes);

        return true;
    }

    /**
     * おすすめ商品テーブル作成.
     *
     * @param Schema $schema
     */
    protected function createCategoryContentForOldVersion(Schema $schema)
    {
        $table = $schema->createTable(self::NAME);
        $table->addColumn('category_id', 'integer', array(
            'notnull' => true,
        ));

        $table->addColumn('content', 'text', array(
            'notnull' => false,
        ));

        $table->setPrimaryKey(array('category_id'));
    }

    /**
     * Get metadata.
     *
     * @param EntityManager $em
     *
     * @return array
     */
    protected function getMetadata(EntityManager $em)
    {
        $meta = array();
        foreach ($this->entities as $entity) {
            $meta[] = $em->getMetadataFactory()->getMetadataFor($entity);
        }

        return $meta;
    }
}
