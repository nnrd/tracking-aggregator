<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tracking;

/**
 * TrackingSearch represents the model behind the search form about `app\models\Tracking`.
 */
class TrackingSearch extends Tracking
{

    const TRACKER_STATUS_NOT_DELIVERED = 1000;
    const TRACKER_STATUS_STUCK = 1001;


    public $created_range;
    public $updated_range;
    public $tracked_range;
    public $delivered_range;

    public $filename;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'status', 'tracker_status', 'upload_id'], 'integer'],
            [['order_id', 'track_number', 'first_name', 'last_name', 'data', 'created_at', 'updated_at', 'tracked_at', 'delivered_at'], 'safe'],
            [['created_range','updated_range', 'tracked_range', 'delivered_range', 'filename', 'carrier'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Tracking::find()->joinWith(['category', 'uploadOperation'])
            ->andWhere(['<>', 'tracking.status', self::STATUS_DELETED]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['category'] = [
            'asc'  => ['category.title' => SORT_ASC],
            'desc' => ['category.title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['filename'] = [
            'asc'  => ['upload_operation.filename' => SORT_ASC],
            'desc' => ['upload_operation.filename' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'tracking.id' => $this->id,
            'tracking.status' => $this->status,
            'upload_id' => $this->upload_id,
        ]);


        $this->filterTrackerStatus($query);


        $this->filterDateField($query,'created_range', 'tracking.created_at');
        $this->filterDateField($query,'updated_range', 'tracking.updated_at');
        $this->filterDateField($query,'tracked_range', 'tracking.tracked_at');
        $this->filterDateField($query,'delivered_range', 'tracking.delivered_at');

        if(!empty($this->category_id))
        {
            // with subnodes
            $_node       = Category::findOne(['id' => $this->category_id]);
            $_categories = $_node->children()->asArray()->all();
            array_push($_categories, $_node->toArray());
            $_categories = \yii\helpers\ArrayHelper::map($_categories, 'id', 'id');
            $query->andFilterWhere([self::tableName() . '.category_id' => $_categories]);
        }

        $query
            ->andFilterWhere(['like', 'order_id', $this->order_id])
            ->andFilterWhere(['like', 'track_number', $this->track_number])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'carrier', $this->carrier])
            ->andFilterWhere(['like', 'upload_operation.filename', $this->filename]);

        return $dataProvider;
    }

    protected function filterDateField($query, $fieldName, $tableFieldName)
    {
        if (isset($this->$fieldName) && strpos($this->$fieldName, ' - ') !== false)
        {
            list($start_date, $end_date) = explode(' - ', $this->$fieldName);
            $start_time = strtotime($start_date);
            if ($start_time)
            {
                $query->andFilterWhere(['>=', $tableFieldName, strtotime('midnight', $start_time)]);
            }
            $end_time = strtotime($end_date);
            if ($start_time)
            {
                $query->andFilterWhere(['<', $tableFieldName, strtotime('tomorrow',$end_time)]);
            }
            //$this->$fieldName = null;
        }
    }

    public static function getTrackerStatusLabels()
    {
        $labels = parent::getTrackerStatusLabels();

        $labels[self::TRACKER_STATUS_NOT_DELIVERED] = 'All not delivered';
        $labels[self::TRACKER_STATUS_STUCK] = 'All failed';

        return $labels;
    }


    protected function filterTrackerStatus($query)
    {
        $codes = self::getTrackerStatusCodes();

        switch ($this->tracker_status)
        {
            case self::TRACKER_STATUS_NOT_DELIVERED:
                $query->andWhere('tracker_status IS NOT NULL')->andFilterWhere(['<>', 'tracker_status', $codes['delivered']]);
                return;
            case self::TRACKER_STATUS_STUCK:
                $query->andFilterWhere([
                    'tracker_status' => [
                        $codes['undelivered'],
                        $codes['exception'],
                        $codes['expired'],
                    ]
                ]);
                return;
            default:
                $query->andFilterWhere(['tracker_status' => $this->tracker_status]);
        }
    }

}
