<?php

use yii\helpers\Html;
use app\widgets\bootstrapTreeview\BootstrapTreeviewWidget;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= BootstrapTreeviewWidget::widget([
        'modelClass' => $modelClass,
        'nameAttribute' => $name,
    ]);?>

</div>
