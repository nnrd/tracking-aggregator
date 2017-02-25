<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ApiOperation */

$this->title = 'Create Api Operation';
$this->params['breadcrumbs'][] = ['label' => 'Api Operations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-operation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
