<?php

use app\components\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use app\models\Tracking;
use app\models\TrackingSearch;
use app\models\Category;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TrackingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trackings';
$this->params['breadcrumbs'][] = $this->title;

$carriers = Tracking::getCarrierLabels();
$statuses = Tracking::getStatusLabels();
$statusWarnings = Tracking::getStatusWarningLevels();
$trackerStatuses = Tracking::getTrackerStatusLabels();
$trackerStatusWarnings = Tracking::getTrackerStatusWarningLevels();

?>
<div class="tracking-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Tracking', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Upload Trackings', ['uploads/upload'], ['class' => 'btn btn-success']) ?>
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
                'headerOptions' => ['style' => 'width:10%'],
            ],
            'track_number',
            [
                'attribute' => 'carrier',
                'filter'    => $carriers,
                'value'     => function(Tracking $model) use ($carriers)
                {
                    return (isset($model->carrier) && array_key_exists($model->carrier, $carriers))
                        ? $carriers[$model->carrier]
                        : $model->carrier;
                }
            ],
            'first_name',
            'last_name',
            [
                'attribute' => 'status',
                'format'    => 'raw',
                'filter'    => $statuses,
                'value'     => function(Tracking $model) use ($statuses, $statusWarnings)
                {
                    return (isset($model->status) && array_key_exists($model->status, $statuses))
                        ? Html::bslabel($statuses[$model->status], $statusWarnings[$model->status])
                        : Html::bslabel($model->status, 'unimportant');
                }
            ],
            [
                'attribute' => 'tracker_status',
                'filter'    => $searchModel->getTrackerStatusLabels(),
                'format'    => 'raw',
                'value'     => function(Tracking $model) use ($trackerStatuses, $trackerStatusWarnings)
                {
                    return (isset($model->tracker_status) && array_key_exists($model->tracker_status, $trackerStatuses))
                        ? Html::bslabel($trackerStatuses[$model->tracker_status], $trackerStatusWarnings[$model->tracker_status])
                        : Html::bslabel($model->tracker_status, 'unimportant');
                }
            ],
            [
                'attribute' => 'filename',
                'format' => 'html',
                'value'     => function(Tracking $model)
                {
                    return ($model->upload_id && $model->uploadOperation)
                        ? Html::a($model->uploadOperation->filename, ['uploads/view', 'id' => $model->uploadOperation->id])
                        : $model->upload_id;
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
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
                'attribute' => 'updated_at',
                'format' => 'raw',
                'value' => function(Tracking $model) {
                    return Yii::$app->formatter->asDateTime($model->updated_at);
                },
                'filter' => DateRangePicker::widget([
                    'model'          => $searchModel,
                    'attribute'      => 'updated_range',
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
                'format' => 'raw',
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
                'format' => 'raw',
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
