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

    public $created_range;
    public $tracked_range;
    public $delivered_range;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'status', 'tracker_status', 'upload_id'], 'integer'],
            [['order_id', 'track_number', 'first_name', 'last_name', 'data', 'created_at', 'updated_at', 'tracked_at', 'delivered_at'], 'safe'],
            [['created_range', 'tracked_range', 'delivered_range'], 'safe'],
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
        $query = Tracking::find()->joinWith(['category']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['category'] = [
            'asc'  => ['category.title' => SORT_ASC],
            'desc' => ['category.title' => SORT_DESC],
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
            'status' => $this->status,
            'tracker_status' => $this->tracker_status,
            'upload_id' => $this->upload_id,
        ]);

        $this->filterDateField($query,'created_range', 'created_at');
        $this->filterDateField($query,'tracked_range', 'tracked_at');
        $this->filterDateField($query,'delivered_range', 'delivered_at');

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
            ->andFilterWhere(['like', 'last_name', $this->last_name]);

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
}