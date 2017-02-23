<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\UploadOperation */

$statusLabels = $model->getStatusLabels();

if (!$insideTab)
{
    $this->title = $model->id;
    $this->params['breadcrumbs'][] = ['label' => 'Upload Operations', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="upload-operation-view">

    <?php if (!$insideTab) { ?>
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php } ?>

    <?php if ($model->hasErrors()) { ?>
        <h3>File <?= $model->filename ?> has errors:</h3>
        <?php foreach($model->getErrors() as $errors) { ?>
            <?php foreach($errors as $error) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger">
                            <?= print_r($error, true) ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } else { ?>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'filename',
                'mime',
                'handler',
                [
                    'attribute' => 'status',
                    'value' => isset($model->status) && array_key_exists($model->status, $statusLabels) ? $statusLabels[$model->status] : $model->status,
                ],
                [
                    'attribute' => 'uploaded_by',
                    'format' => 'html',
                    'value' => isset($model->uploaded_by) && $model->user ? Html::a($model->user->username, ['user/view', 'id' => $model->user->id]) : $model->uploaded_by,
                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    <?php } ?>
</div>
