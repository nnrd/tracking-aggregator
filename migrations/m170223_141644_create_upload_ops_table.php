<?php

use yii\db\Migration;

/**
 * Handles the creation of table `upload_ops`.
 */
class m170223_141644_create_upload_ops_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('upload_operation', [
            'id' => $this->primaryKey(),
            'filename' => 'varchar(255)',
            'format' => 'tinyint', // File format csv, xls etc
            'status' => 'tinyint',
            'uploaded_by' => 'int(11)', // Link to user performed upload
            'created_at' => 'int(11)',
            'updated_at' => 'int(11)',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('upload_operation');
    }
}
