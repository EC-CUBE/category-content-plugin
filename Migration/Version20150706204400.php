<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150706204400 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->createPluginTable($schema);
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('plg_category_content');
    }

    protected function createPluginTable(Schema $schema)
    {
        $table = $schema->createTable("plg_category_content");
        $table->addColumn('category_id', 'integer');
        $table->addColumn('content', 'text', array('notnull' => false));
        $table->setPrimaryKey(array('category_id'));
    }
}