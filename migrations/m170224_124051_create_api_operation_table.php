<?php

use yii\db\Migration;

/**
 * Handles the creation of table `api_operation`.
 */
class m170224_124051_create_api_operation_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('api_operation', [
            'id' => $this->primaryKey(),
            'action' => 'char(8) not null',
            'url' => 'varchar(255) not null',
            'path' => 'varchar(255) not null',
            'status' => 'tinyint not null',
            'request' => 'text',
            'code' => 'mediumint(4)',
            'response' => 'text',
            'created_at' => 'int(11)',
            'updated_at' => 'int(11)',
        ]);

        $this->createTable('api_operation_map', [
            'tracking_id' => 'int(11)',
            'api_operation_id' => 'int(11)',
        ]);

        $this->addPrimaryKey('PK-tracking-op', 'api_operation_map', ['tracking_id', 'api_operation_id']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('api_operation');
        $this->dropTable('api_operation_map');
    }
}
