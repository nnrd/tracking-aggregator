<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "upload_operation".
 *
 * @property integer $id
 * @property string $filename
 * @property integer $status
 * @property integer $uploaded_by
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $mime
 * @property string $handler
 */
class UploadOperation extends \yii\db\ActiveRecord
{

    const STATUS_UPLOADED = 0;
    const STATUS_PROCESSED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'upload_operation';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'uploaded_by'], 'integer'],
            [['filename'], 'string', 'max' => 255],
            [['handler'], 'string', 'max' => 255],
            [['mime'], 'string', 'max' => 32],
            [['uploaded_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uploaded_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'filename' => Yii::t('app', 'Filename'),
            'mime' => Yii::t('app', 'File MIME type'),
            'handler' => Yii::t('app', 'MIME type handler'),
            'status' => Yii::t('app', 'Status'),
            'uploaded_by' => Yii::t('app', 'Uploaded By'),
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uploaded_by']);
    }



    public function getStatusLabels()
    {
        return [
            self::STATUS_UPLOADED => Yii::t('app', 'Uploaded'),
            self::STATUS_PROCESSED => Yii::t('app', 'Processed'),
        ];
    }
}
