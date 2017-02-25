<?php

use app\components\Html;
use yii\widgets\DetailView;
use app\models\Tracking;

/* @var $this yii\web\View */
/* @var $model app\models\ApiOperation */

$this->title = 'API operation';
$this->params['breadcrumbs'][] = ['label' => 'Api Operations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id;

$statuses = $model->getStatusLabels();
$statusWarnings = $model->getStatusWarningLevels();

$trackerStatuses = Tracking::getTrackerStatusLabels();
$trackerStatusWarnings = Tracking::getTrackerStatusWarningLevels();
$codes = Tracking::getTrackerStatusCodes();


if ($model->parseResponse())
{
    if ($model->trackStatus)
    {
        if (array_key_exists($model->trackStatus, $codes))
        {
            $code = $codes[$model->trackStatus];
            $status = $trackerStatuses[$code];
        }
        else
        {
            $code = false;
            $status = $model->trackStatus;
        }
        $warning = $code !== false ? $trackerStatusWarnings[$code] : 'unimportant';
        $status = Html::bslabel($status, $warning);
    }
    else
    {
        $status = '';
    }

    if ($model->originTrackInfo)
    {
        $originTrackInfo = $model->originTrackInfo;
    }
    else
    {
        $originTrackInfo = false;
    }

    if ($model->destinationTrackInfo)
    {
        $destinationTrackInfo = $model->destinationTrackInfo;
    }
    else
    {
        $destinationTrackInfo = false;
    }
}
?>
<div class="api-operation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'url',
                    'path',
                    'action',
                    [
                        'attribute' => 'status',
                        'format' => 'html',
                        'value' => (isset($model->status) && array_key_exists($model->status, $statuses))
                               ? Html::bslabel($statuses[$model->status], $statusWarnings[$model->status])
                               : Html::bslabel($model->status, 'unimportant'),
                    ],
                    'code',
                    [
                        'attribute' => 'trackStatus',
                        'format' => 'html',
                        'value' => $status,
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>
    <?php if ($destinationTrackInfo) {
        echo '<h2>Destination tracking information</h2>';
        foreach($destinationTrackInfo as $info) {?>
        <div class="alert alert-unimportant">
            <span><strong><?= $info->Date ?></strong> <?= $info->Details ?></span><br>
            <span><strong>Description:</strong> <?= $info->StatusDescription ?></span><br>
        </div>
    <?php } } ?>

    <?php if ($originTrackInfo) {
        echo '<h2>Origin tracking information</h2>';
        foreach($originTrackInfo as $info) {?>
        <div class="alert alert-unimportant">
            <span><strong><?= $info->Date ?></strong> <?= $info->Details ?></span><br>
            <span><strong>Description:</strong> <?= $info->StatusDescription ?></span><br>
        </div>
    <?php } } ?>

    <h2>Tech data</h2>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'request:ntext',
            'response:ntext',
        ],
    ]) ?>

</div>
