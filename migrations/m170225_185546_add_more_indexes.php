<?php

use yii\db\Migration;

class m170225_185546_add_more_indexes extends Migration
{
    public function up()
    {
        $this->createIndex('idx_tree', 'category', 'tree');
        $this->createIndex('idx_lft', 'category', 'lft');
        $this->createIndex('idx_rgt', 'category', 'rgt');
        $this->createIndex('idx_depth', 'category', 'depth');
        $this->createIndex('idx_title', 'category', 'title');
    }

    public function down()
    {
        $this->dropIndex('idx_tree', 'category');
        $this->dropIndex('idx_lft', 'category');
        $this->dropIndex('idx_rgt', 'category');
        $this->dropIndex('idx_depth', 'category');
        $this->dropIndex('idx_title', 'category');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
