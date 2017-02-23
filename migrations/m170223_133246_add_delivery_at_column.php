<?php

use yii\db\Migration;

class m170223_133246_add_delivery_at_column extends Migration
{
    public function up()
    {
        $this->addColumn('tracking', 'delivered_at', 'int(11)');

    }

    public function down()
    {
        $this->dropColumn('tracking', 'delivered_at');
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
