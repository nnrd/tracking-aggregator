<?php

use yii\db\Migration;

class m170225_120944_add_indexes extends Migration
{
    public function up()
    {
        $this->createIndex('idx_category_id', 'tracking', 'category_id');
        $this->createIndex('idx_status', 'tracking', 'status');
        $this->createIndex('idx_tracker_status', 'tracking', 'tracker_status');
        $this->createIndex('idx_created_at', 'tracking', 'created_at');
        $this->createIndex('idx_updated_at', 'tracking', 'updated_at');
        $this->createIndex('idx_tracked_at', 'tracking', 'tracked_at');
        $this->createIndex('idx_delivered_at', 'tracking', 'delivered_at');
        $this->addForeignKey('FK_upload_id', 'tracking', 'upload_id', 'upload_operation', 'id', 'SET NULL', 'CASCADE');


        $this->createIndex('idx_status', 'api_operation', 'status');
        $this->createIndex('idx_created_at', 'api_operation', 'created_at');
        $this->createIndex('idx_updated_at', 'api_operation', 'updated_at');

        $this->createIndex('idx_status', 'upload_operation', 'status');
        $this->createIndex('idx_created_at', 'upload_operation', 'created_at');
        $this->createIndex('idx_updated_at', 'upload_operation', 'updated_at');
    }

    public function down()
    {
        $this->dropIndex('idx_category_id', 'tracking');
        $this->dropIndex('idx_status', 'tracking');
        $this->dropIndex('idx_tracker_status', 'tracking');
        $this->dropIndex('idx_dropd_at', 'tracking');
        $this->dropIndex('idx_updated_at', 'tracking');
        $this->dropIndex('idx_tracked_at', 'tracking');
        $this->dropIndex('idx_delivered_at', 'tracking');
        $this->dropForeignKey('FK_upload_id', 'tracking');


        $this->dropIndex('idx_status', 'api_operation');
        $this->dropIndex('idx_dropd_at', 'api_operation');
        $this->dropIndex('idx_updated_at', 'api_operation');

        $this->dropIndex('idx_status', 'upload_operation');
        $this->dropIndex('idx_dropd_at', 'upload_operation');
        $this->dropIndex('idx_updated_at', 'upload_operation');
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
