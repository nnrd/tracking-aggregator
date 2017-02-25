<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ApiOperation */

$this->title = 'Update Api Operation: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Api Operations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="api-operation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
