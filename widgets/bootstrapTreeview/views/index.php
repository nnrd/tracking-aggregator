<?php
use app\widgets\bootstrapTreeview\BootstrapTreeviewWidgetAsset;
use app\widgets\bootstrapTreeview\BootstrapTreeviewWidget;
use yii\helpers\Html;
use yii\helpers\Json;

BootstrapTreeviewWidgetAsset::register($this);
$this->registerJs(sprintf('BootstrapTreeviewWidget.init(%s)', Json::encode($options)));

$glyphPlus = Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']) . ' ';

?>
<div class="bootstrap-treeview-widget" id="<?= $options['containerId']?>">
    <div class="create hidden">
        <div class="btn-group btn-group-xs" role="group" aria-label="Operations">
            <?= Html::button($glyphPlus . BootstrapTreeviewWidget::m('root'), ['class' => 'btn btn-success button-new-root']) ?>
            <?= Html::button($glyphPlus . BootstrapTreeviewWidget::m('node'), ['class' => 'btn btn-success button-new-node']) ?>
            <?= Html::button($glyphPlus . BootstrapTreeviewWidget::m('subnode'), ['class' => 'btn btn-success button-new-subnode']) ?>
        </div>
    </div>
    <div class="tree"></div>
</div>
