<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "api_operation".
 *
 * @property integer $id
 * @property string $action
 * @property string $url
 * @property string $path
 * @property integer $status
 * @property string $request
 * @property integer $code
 * @property string $response
 */
class ApiOperation extends \yii\db\ActiveRecord
{

    const STATUS_REQUESTED = 0;
    const STATUS_RESPONDED = 1;


    const SUG_HOLDOFF = 1;

    public $suggestion = 0;

    public $trackStatus;
    public $originTrackInfo;
    public $destinationTrackInfo;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_operation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url', 'path', 'status', 'action'], 'required'],
            [['action'], 'string', 'max' => 8],
            [['code'], 'integer'],
            [['status'], 'integer', 'max' => 255],
            [['request', 'response'], 'string'],
            [['url', 'path'], 'string', 'max' => 255],
        ];
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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tracking_id' => Yii::t('app', 'Tracking ID'),
            'status' => Yii::t('app', 'Status'),
            'url' => Yii::t('app', 'Request url'),
            'path' => Yii::t('app', 'Request path'),
            'request' => Yii::t('app', 'Request body'),
            'code' => Yii::t('app', 'Response code'),
            'response' => Yii::t('app', 'Response body'),
            'trackStatus' => Yii::t('app', 'Tracking status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrackings()
    {
        return $this->hasMany(Tracking::className(), ['id' => 'tracking_id'])
            ->viaTable('api_operation_map', ['api_operation_id' => 'id']);
    }

    public function linkTrackings($trackings)
    {
        $values = [];
        foreach($trackings as $tracking)
        {
            $values[] = sprintf('(%d, %d)', (int) $tracking->id, (int) $this->id);
        }

        $command = Yii::$app->db->createCommand('INSERT INTO `api_operation_map` (tracking_id, api_operation_id) VALUES ' . implode(',', $values));
        return $command->execute();
    }

    public static function getStatusLabels()
    {
        return [
            self::STATUS_REQUESTED => 'Requested',
            self::STATUS_RESPONDED => 'Responded',
        ];
    }

    public static function getStatusWarningLevels()
    {
        return [
            self::STATUS_REQUESTED => 'warning',
            self::STATUS_RESPONDED => 'success',
        ];
    }

    public function parseResponse()
    {
        if ($this->code == 200 || $this->code == 201)
        {
            $json = json_decode($this->response);
            if ($json)
            {
                if (isset($json->data->status)) {
                    $this->trackStatus = $json->data->status;
                }
                if (isset($json->data->origin_info->trackinfo)) {
                    $this->originTrackInfo = $json->data->origin_info->trackinfo;
                }
                if (isset($json->data->destination_info->trackinfo)) {
                    $this->destinationTrackInfo = $json->data->destination_info->trackinfo;
                }
                return true;
            }
        }
        return false;
    }
}
