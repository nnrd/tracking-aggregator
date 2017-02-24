<?php
namespace app\components\trackerManager;

use Yii;

class Manager extends \yii\base\Component
{
    const HANDLER_DUMMY = 0;
    const HANDLER_TRACKINGMORE = 1;

    public $handlerClass;
    protected $handlerInstance;

    public function init()
    {
        $handlerParams = [
            DummyHandler::class => [ 'params' => [], ],
            TrackingmoreHandler::class => [
                'params' => Yii::$app->params['trackers']['trackingmore'],
            ],
        ];


        $class = $this->handlerClass;
        $this->handlerInstance = new $class([
            'requestParams' => $handlerParams[$class]['params']
         ]);
    }

    public function getApi()
    {
        return $this->handlerInstance;
    }
}
