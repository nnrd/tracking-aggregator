<?php

namespace app\widgets\bootstrapTreeview;

/**
 * Main backend application asset bundle.
 */
class BootstrapTreeviewWidgetAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/widgets/bootstrapTreeview/assets';
    public $js = [
        'js/bootstrap_treeview_widget.js',
    ];
    public $css = [
        'css/bootstrap_treeview_widget.css',
    ];
    public $depends = [
        BootstrapTreeviewAsset::class,
    ];
    public $publishOptions = [
        'forceCopy' => true,
    ];
}
