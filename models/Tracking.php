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
        $scenarios[self::SCENARIO_TRACK] = ['delivered_at', 'tracked_at', 'tracker_status'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'track_number'], 'required'],
            [['category_id', 'status', 'tracker_status', 'upload_id', 'tracked_at', 'delivered_at'], 'integer'],
            [['order_id', 'track_number'], 'string', 'max' => 30],
            [['first_name', 'last_name'], 'string', 'max' => 40],
            [['track_number'], 'unique'],
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
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),
            'tracked_at' => Yii::t('app', 'Tracked'),
            'delivered_at' => Yii::t('app', 'Delivered'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApiOperations()
    {
        return $this->hasMany(ApiOperation::className(), ['id' => 'api_operation_id'])
            ->viaTable('api_operation_map', ['tracking_id', 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUploadOperation()
    {
        return $this->hasOne(UploadOperation::className(), ['id' => 'upload_id']);
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
            "pending",
            "notfound",
            "transit",
            "pickup",
            "delivered",
            "undelivered",
            "exception",
            "expired",
        ];
    }

    public static function getTrackerStatusCodes()
    {
        return array_flip(self::getTrackerStatusLabels());
    }

    public static function getCarrierLabels()
    {
        return [
            "139express" => "139 ECONOMIC Package",
            "17postservice	17" => "Post Service",
            "acscourier" => "ACS Courier",
            "adicional" => "Adicional Logistics",
            "aramex" => "Aramex",
            "armenia-post" => "Armenia post",
            "asendia-de" => "Asendia Germany",
            "austria-post" => "Austrian Post",
            "azerbaijan-post" => "Azerbaijan post",
            "bartolini" => "BRT Bartolini",
            "belgium-post" => "Belgium post",
            "belpochta" => "Belarus post",
            "bosnia-and-herzegovina-post" => "Bosnia and Herzegovina post",
            "brazil-correios" => "Brazil Correios",
            "canada-post" => "Canada post",
            "canpar" => "Canpar Courier",
            "china-ems" => "China EMS",
            "chronopost" => "France EMS - Chronopost",
            "cnexps" => "CNE Express",
            "colissimo" => "French Post - Colissimo",
            "correo-argentino" => "Argentina post",
            "correos-bolivia" => "Bolivia post",
            "correos-spain" => "correos",
            "ctt" => "Portugal post - CTT",
            "czech-post" => "Česká Pošta",
            "denmark-post" => "Denmark post",
            "dhl" => "DHL",
            "dhl-active" => "DHL Active Tracing",
            "dhl-es" => "DHL Spain Domestic",
            "dhl-germany" => "Deutsche Post DHL",
            "dhl-poland" => "DHL Poland Domestic",
            "dhlglobalmail" => "DHL eCommerce",
            "dhlparcel-nl" => "DHL Parcel Netherlands",
            "dpd" => "DPD",
            "ecargo-asia" => "Ecargo",
            "emirates-post" => "Emirates Post",
            "fedex" => "Fedex",
            "finland-posti" => "Finland post - Posti",
            "georgian-post" => "Georgia post",
            "gls" => "GLS",
            "gls-italy" => "GLS Italy",
            "greece-post" => "ELTA Hellenic Post",
            "hong-kong-post" => "Hong Kong Post",
            "iceland-post" => "Iceland post",
            "israel-post" => "Israel post",
            "israel-post" => "Israel post",
            "japan-post" => "Japan post",
            "jcex" => "JCEX",
            "kazpost" => "Kazakhstan post",
            "kerry-logistics" => "Kerry Express",
            "korea-post" => "Korea Post",
            "kyrgyzpost" => "Kyrgyzstan post",
            "latvijas-pasts" => "Latvia post",
            "lietuvos-pastas" => "Lithuania post",
            "luxembourg-post" => "Luxembourg post",
            "macao-post" => "Macao Post",
            "malaysia-post" => "Malaysia post",
            "matkahuolto" => "Matkahuolto",
            "moldova-post" => "Moldova post",
            "omniva" => "Estonia post",
            "pfcexpress" => "PFC Express",
            "poczta-polska" => "Poland post",
            "posten-norge" => "Posten Norge",
            "purolator" => "Purolator",
            "saudi-post" => "Saudi Post",
            "serbia-post" => "Serbia post",
            "sf-express" => "S.F Express",
            "sfb2c" => "S.F International",
            "sfcservice" => "SFC Service",
            "slovakia-post" => "Slovakia post",
            "sweden-posten" => "Sweden Posten",
            "swiss-post" => "Swiss Post",
            "taqbin-jp" => "Yamato Japan",
            "taxydromiki" => "Geniki Taxydromiki",
            "tnt" => "TNT",
            "tunisia-post" => "Tunisia post",
            "turkey-post" => "Turkey post",
            "ups" => "UPS",
            "vietnam-post" => "Vietnam post",
            "wishpost" => "WishPost",
            "yanwen" => "YANWEN",
        ];
    }

    public function getCarrierCodes()
    {
        return array_flip(self::getCarrierLabels());
    }

}
