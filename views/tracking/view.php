<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Category;

/* @var $this yii\web\View */
/* @var $model app\models\Tracking */

$this->title = $model->id;
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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_id',
            [
                'attribute' => 'category_id',
                'value' => isset($model->category_id) ? implode(' / ', Category::find()
                                                                ->andFilterWhere(['id' =>  $model->category_id])->one()->getPath(true)) : null,
            ],
            'track_number',
            'first_name',
            'last_name',
            [
                'attribute' => 'status',
                'value' => isset($model->status) ? $model->getStatusLabels()[$model->status] : null,
            ],
            [
                'attribute' => 'tracker_status',
                'value' => isset($model->status) ? $model->getTrackerStatusLabels()[$model->status] : null,
            ],
            [
                'attribute' => 'upload_id',
                'label' => Yii::t('app', 'Filename'),
                'format' => 'html',
                'value' => ($model->upload_id && $model->uploadOperation)
                ? Html::a($model->uploadOperation->filename, ['uploads/view', 'id' => $model->uploadOperation->id])
                : $model->upload_id,
            ],
            'created_at:datetime',
            'updated_at:datetime',
            'tracked_at:datetime',
        ],
    ]) ?>

</div>
