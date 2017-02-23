<?php

use yii\helpers\Html;
use yii\grid\GridView;

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
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'order_id',
            'category_id',
            'track_number',
            'first_name',
            // 'last_name',
            // 'status',
            // 'tracker_status',
            // 'upload_id',
            // 'data:ntext',
            // 'created_at',
            // 'updated_at',
            // 'tracked_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
