<?php

use app\components\Html;
use yii\widgets\DetailView;
use app\models\Tracking;

/* @var $this yii\web\View */
/* @var $model app\models\ApiOperation */

$fmt = Yii::$app->formatter;

$statuses = $model->getStatusLabels();
$statusWarnings = $model->getStatusWarningLevels();

$trackerStatuses = Tracking::getTrackerStatusLabels();
$trackerStatusWarnings = Tracking::getTrackerStatusWarningLevels();
$codes = Tracking::getTrackerStatusCodes();



?>
<div class="api-operation-widget">
    <div class="nav alert alert-unimportant">
        <div class="nav navbar-nav navbar-left">
            <span><strong>Date: </strong><?= $fmt->asDatetime($model->created_at)?></span><br>
            <span><strong>Status: </strong>
                <?= (isset($model->status) && array_key_exists($model->status, $statuses))
                  ? Html::bslabel($statuses[$model->status], $statusWarnings[$model->status])
                  : Html::bslabel($model->status, 'unimportant');?>
            </span>
            <?php if ($model->parseResponse() && $model->trackStatus) {
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
            ?>
            <br><span><strong>Tracking status: </strong><?= Html::bslabel($status, $warning)?></span><br>
            <?php } ?>

        </div>
        <span class="nav navbar-nav pull-right"><?= Html::a('View', ['api-operation/view', 'id' => $model->id], ['class' => 'btn btn-success'])?></span>
    </div>
</div>
