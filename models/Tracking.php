<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "tracking".
 *
 * @property integer $id
 * @property string $order_id
 * @property integer $category_id
 * @property string $track_number
 * @property string $first_name
 * @property string $last_name
 * @property integer $status
 * @property integer $tracker_status
 * @property integer $upload_id
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $tracked_at
 */
class Tracking extends \yii\db\ActiveRecord
{

    const SCENARIO_FORM = 'form';
    const SCENARIO_TRACK = 'track';


    /*
     * Statuses for tracking ops
     */
    const STATUS_DISABLED = 0; // Do not check tracking
    const STATUS_NORMAL = 1; // Check as usual - check if possible
    const STATUS_URGENT = 2; // Urgent checking - check these first anyway

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tracking';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_FORM] = ['order_id', 'category_id', 'status', 'first_name', 'last_name', 'track_number'];
        $scenarios[self::SCENARIO_TRACK] = ['tracked_at', 'tracker_status'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['category_id', 'status', 'tracker_status', 'upload_id', 'tracked_at'], 'integer'],
            [['order_id', 'track_number'], 'string', 'max' => 30],
            [['first_name', 'last_name'], 'string', 'max' => 40],
            [['order_id'], 'unique'],
            [['data'], 'string'],
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
            'order_id' => Yii::t('app', 'Order ID'),
            'category_id' => Yii::t('app', 'Category'),
            'track_number' => Yii::t('app', 'Tracking Number'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'status' => Yii::t('app', 'Status'),
            'tracker_status' => Yii::t('app', 'Tracking Status'),
            'upload_id' => Yii::t('app', 'Upload ID'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'tracked_at' => Yii::t('app', 'Tracked At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public static function getStatusLabels()
    {
        return [
            self::STATUS_DISABLED => Yii::t('app', 'Disabled'),
            self::STATUS_NORMAL => Yii::t('app', 'Normal'),
            self::STATUS_URGENT => Yii::t('app', 'Urgent'),
        ];
    }

    public static function getTrackerStatusLabels()
    {
        return [
        ];
    }
}
