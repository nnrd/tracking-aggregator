<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UploadOperation */

$this->title = 'Create Upload Operation';
$this->params['breadcrumbs'][] = ['label' => 'Upload Operations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="upload-operation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
