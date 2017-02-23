<?php

use app\components\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use app\models\Tracking;
use app\models\Category;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TrackingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trackings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tracking-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Tracking', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Upload Trackings', ['upload'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'order_id',
            [
                'attribute' => 'category_id',
                'label' => 'Category',
                'filter' => Category::getPathsList(),
                'format' => 'raw',
                'value' => function(Tracking $model)
                {
                    $categories = $model->category->getPath(true);
                    array_walk($categories, function(&$v, $i) {
                        $v = Html::bslabel($v);
                    });
                    return implode(' / ', $categories);
                },
                'headerOptions' => ['style' => 'width:20%'],
            ],
            'track_number',
            'first_name',
            'last_name',
            [
                'attribute' => 'status',
                'filter'    => Tracking::getStatusLabels(),
                'value'     => function(Tracking $model)
                {
                    return ($model->status && array_key_exists($model->status, Tracking::getStatusLabels()))
                        ? Tracking::getStatusLabels()[$model->status]
                        : $model->status;
                }
            ],
            [
                'attribute' => 'tracker_status',
                'filter'    => Tracking::getTrackerStatusLabels(),
                'value'     => function(Tracking $model)
                {
                    return ($model->tracker_status && array_key_exists($model->tracker_status, Tracking::getTrackerStatusLabels()))
                        ? Tracking::getStatusLabels()[$model->tracker_status]
                        : $model->tracker_status;
                }
            ],
            'upload_id',
            [
                'attribute' => 'created_at',
                'format' => 'html',
                'value' => function(Tracking $model) {
                    return Yii::$app->formatter->asDateTime($model->created_at);
                },
                'filter' => DateRangePicker::widget([
                    'model'          => $searchModel,
                    'attribute'      => 'created_range',
                    'convertFormat'  => true,
                    'pluginOptions' => [
                        'locale'=>[
                            'format'=>'Y-m-d',
                        ]
                    ],
                ]),
            ],
            [
                'attribute' => 'tracked_at',
                'format' => 'html',
                'value' => function(Tracking $model) {
                    return Yii::$app->formatter->asDateTime($model->tracked_at);
                },
                'filter' => DateRangePicker::widget([
                    'model'          => $searchModel,
                    'attribute'      => 'tracked_range',
                    'convertFormat'  => true,
                    'pluginOptions' => [
                        'locale'=>[
                            'format'=>'Y-m-d',
                        ]
                    ],
                ]),
            ],
            [
                'attribute' => 'delivered_at',
                'format' => 'html',
                'value' => function(Tracking $model) {
                    return Yii::$app->formatter->asDateTime($model->delivered_at);
                },
                'filter' => DateRangePicker::widget([
                    'model'          => $searchModel,
                    'attribute'      => 'delivered_range',
                    'convertFormat'  => true,
                    'pluginOptions' => [
                        'locale'=>[
                            'format'=>'Y-m-d',
                        ]
                    ],
                ]),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
