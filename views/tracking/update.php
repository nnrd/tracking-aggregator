<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tracking */

$this->title = 'Update Tracking: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trackings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tracking-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
