<?php

use yii\widgets\ActiveForm;
use app\components\Html;
use app\models\Category;


$this->title = 'Upload Trackings';
$this->params['breadcrumbs'][] = ['label' => 'Trackings', 'url' => ['tracking/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="upload-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>



    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'category_id')->dropDownList(Category::getPathsList()) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'subcategory_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'files[]')->fileInput(['multiple' => true, 'class' => '']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'create_subcategory')->checkbox() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'skipLines')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Upload', ['class' => 'btn btn-success']) ?>
    </div>


    <?php ActiveForm::end() ?>
</div>
