<?php

use app\components\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use app\models\Tracking;
use app\models\TrackingSearch;
use app\models\Category;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TrackingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trackings check starting';
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="tracking-index">

    <h1><?= Html::encode($this->title) ?></h1>
</div>
