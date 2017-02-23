<?php

use yii\widgets\ActiveForm;
use app\components\Html;
use app\models\Category;


$this->title = 'Uploaded Trackings';
$this->params['breadcrumbs'][] = ['label' => 'Trackings', 'url' => ['tracking/index']];
$this->params['breadcrumbs'][] = ['label' => 'Upload Trackings', 'url' => ['uploads/upload']];
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="uploaded-info">
    <?php
    foreach($operations as $operation)
    {
        echo $this->render('view', ['model' => $operation, 'insideTab' => true]);
    }
    ?>
</div>
