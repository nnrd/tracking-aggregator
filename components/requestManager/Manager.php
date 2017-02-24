<?php
namespace app\components\requestManager;

use Yii;

class  Manager extends \yii\base\Component
{
    public $handlerClass;

    protected $handler;

    public function init()
    {
        $class = $this->handlerClass;
        $this->handler = new $class([
            'baseUrl' => Yii::$app->param['Tracker API']['url'],
            'headers' => [
                'Accept' => 'application/json',
                'Trackingmore-Api-Key' => Yii::$app->param['Tracker API']['token'],
            ],
        ]);
    }

    public function get($path, $data = null)
    {
        return $this->handler->get($path, $data);
    }

    public function post($path, $data = null)
    {
        return $this->handler->post($path, $data);
    }

    public function delete($path, $data = null)
    {
        return $this->handler->delete($path, $data);
    }
}
