<?php

use yii\db\Migration;

class m170223_190456_fix_tracking_indexes extends Migration
{
    public function up()
    {
        $this->dropIndex('order_id', 'tracking');
        $this->createIndex('track_number', 'tracking', 'track_number', true);
    }

    public function down()
    {
        $this->dropIndex('track_number', 'tracking');
        $this->createIndex('order_id', 'tracking', 'order_id', true);
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
