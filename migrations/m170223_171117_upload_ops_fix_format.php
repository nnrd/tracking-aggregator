<?php

use yii\db\Migration;

class m170223_171117_upload_ops_fix_format extends Migration
{
    public function up()
    {
        $this->dropColumn('upload_operation', 'format');
        $this->addColumn('upload_operation', 'mime', 'varchar(32)');
        $this->addColumn('upload_operation', 'handler', 'varchar(255)');
    }

    public function down()
    {
        $this->addColumn('upload_operation', 'format', 'tinyint');
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
