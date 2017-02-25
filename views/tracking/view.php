<?php

use app\components\Html;
use yii\widgets\DetailView;
use app\models\Category;

/* @var $this yii\web\View */
/* @var $model app\models\Tracking */

$carriers = $model->getCarrierLabels();

$this->title = $model->track_number ? "{$model->track_number} (id: {$model->id})" : "id: {$model->id}";
$this->params['breadcrumbs'][] = ['label' => 'Trackings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tracking-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'order_id',
                    [
                        'attribute' => 'category_id',
                        'value' => isset($model->category_id) ? implode(' / ', Category::find()->andFilterWhere(['id' =>  $model->category_id])->one()->getPath(true)) : null,
                    ],
                    'first_name',
                    'last_name',
                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'track_number',
                    [
                        'attribute' => 'carrier',
                        'format' => 'html',
                        'value' => isset($model->carrier) && array_key_exists($model->carrier, $carriers) ? $model->getCarrierLabels()[$model->carrier] : $model->carrier,
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'html',
                        'value' => isset($model->status) ? Html::bslabel($model->getStatusLabels()[$model->status], $model->getStatusWarningLevels()[$model->status]) : null,
                    ],
                    [
                        'attribute' => 'tracker_status',
                        'format' => 'html',
                        'value' => isset($model->status) ? Html::bslabel($model->getTrackerStatusLabels()[$model->status], $model->getTrackerStatusWarningLevels()[$model->status]) : null,
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'tracked_at:datetime',
                    'created_at:datetime',
                    'updated_at:datetime',
                    [
                        'attribute' => 'upload_id',
                        'label' => Yii::t('app', 'Filename'),
                        'format' => 'html',
                        'value' => ($model->upload_id && $model->uploadOperation)
                               ? Html::a($model->uploadOperation->filename, ['uploads/view', 'id' => $model->uploadOperation->id])
                               : $model->upload_id,
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <?php
    $apiOps = $model->apiOperations;
    if ($apiOps) { ?>
        <h2>Tracker operations</h2>
        <div class="row">
        <?php
        foreach($apiOps as $op) {
            printf('<div class="col-md-4">%s</div>', $this->render('/api-operation/widget', ['model' => $op]));
        }
        ?>
        </div>
    <?php } ?>

</div>
