<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ApiOperationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-operation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'action') ?>

    <?= $form->field($model, 'url') ?>

    <?= $form->field($model, 'path') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'request') ?>

    <?php // echo $form->field($model, 'code') ?>

    <?php // echo $form->field($model, 'response') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
