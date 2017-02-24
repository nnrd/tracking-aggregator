<?php

use yii\db\Migration;

class m170224_094405_add_tracking_carrier extends Migration
{
    public function up()
    {
        $this->addColumn('tracking', 'carrier', 'varchar(30)');
    }

    public function down()
    {
        $this->dropColumn('tracking', 'carrier');
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
