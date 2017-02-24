<?php
namespace app\components\trackerManager;

use Yii;

class  Manager extends \yii\base\Component
{
    const HANDLER_DUMMY = 0;
    const HANDLER_TRACKINGMORE = 1;

    public $handler;
    protected $handlerIncstance;

    public function init()
    {
        $handlerParams = [
            self::HANDLER_DUMMY => [
                'class' => DummyHandler::class,
                'params' => [],
            ],
            self::HANDLER_TRACKINGMORE => [
                'class' => TrackingmoreHandler::class,
                'params' => Yii::$app->params['trackers']['trackingmore'],
            ],
        ];

        $class = $handlerParams[$this->handler]['class'];
        $this->handlerInstance = new $class(['requestParams' => $handlerParams[$this->handler]['params']]);
    }

    public function getApi()
    {
        return $this->handlerInstance;
    }
}
