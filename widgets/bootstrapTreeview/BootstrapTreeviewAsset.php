<?php

namespace app\widgets\bootstrapTreeview;

/**
 * Main backend application asset bundle.
 */
class BootstrapTreeviewAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/bootstrap-treeview/public';
    public $css = [
        'css/bootstrap-treeview.css',
    ];
    public $js = [
        'js/bootstrap-treeview.js',
    ];
    public $depends = [
        \yii\web\YiiAsset::class,
        \yii\bootstrap\BootstrapAsset::class,
        \yii\jui\JuiAsset::class,
    ];
}
