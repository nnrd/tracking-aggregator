<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UploadOperation */

$this->title = 'Update Upload Operation: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Upload Operations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="upload-operation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
