<?php

use app\components\Html;
use yii\grid\GridView;

use app\models\ApiOperation;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ApiOperationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Api Operations';
$this->params['breadcrumbs'][] = $this->title;

$statuses = $searchModel->getStatusLabels();
$statusWarnings = $searchModel->getStatusWarningLevels();

?>
<div class="api-operation-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width:50px'],
            ],
            [
                'attribute' => 'action',
                'format'    => 'html',
                'filter'    => ['get' => 'get', 'post' => 'post', 'delete' => 'delete'],
            ],
            'url',
            'path',
            [
                'attribute' => 'status',
                'format'    => 'html',
                'filter'    => $statuses,
                'value'     => function(ApiOperation $model) use ($statuses, $statusWarnings)
                {
                    return (isset($model->status) && array_key_exists($model->status, $statuses))
                        ? Html::bslabel($statuses[$model->status], $statusWarnings[$model->status])
                        : Html::bslabel($model->status, 'unimportant');
                }
            ],
            'request:ntext',
            'code',
            // 'response:ntext',
            'created_at:datetime',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
            ],
        ],
    ]); ?>
</div>
