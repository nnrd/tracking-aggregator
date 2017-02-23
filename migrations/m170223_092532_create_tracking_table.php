<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tracking`.
 */
class m170223_092532_create_tracking_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('tracking', [
            'id' => $this->primaryKey(),
            'order_id' => 'varchar(30) not null unique',
            'category_id' => 'int(11)',
            'track_number' => 'varchar(30)',
            'first_name' => 'varchar(40)',
            'last_name' => 'varchar(40)',
            'status' => 'tinyint', // Internal state for tracking (track, skip etc)
            'tracker_status' => 'int(11)', // Tracker status (sent, delivered etc)
            'upload_id' => 'int(11)', // Link to uploaded file info
            'data' => 'text', // Serialized supplementary data
            'created_at' => 'int(11)',
            'updated_at' => 'int(11)',
            'tracked_at' => 'int(11)', // Last update from tracker api
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('tracking');
    }
}
