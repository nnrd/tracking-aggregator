<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `category`.
 */
class m161108_104240_create_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('category', [
            'id' => Schema::TYPE_PK,
            'tree' => Schema::TYPE_INTEGER,
            'lft' => Schema::TYPE_INTEGER . ' NOT NULL',
            'rgt' => Schema::TYPE_INTEGER . ' NOT NULL',
            'depth' => Schema::TYPE_INTEGER . ' NOT NULL',
            'title' => Schema::TYPE_STRING . ' NOT NULL',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('category');
    }
}
