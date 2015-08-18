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
        $schema->dropTable('category_content');
    }

    protected function createPluginTable(Schema $schema)
    {
        $table = $schema->createTable("category_contnet");
        $table
            ->addColumn('category_id', 'integer', array(
                'notnull' => true,
            ))
            ->addColumn('content', 'text')
        ;
    }
}