<?php
namespace app\components\requestManager;

class  Manager extends \yii\base\Component
{
    public $handlerClass;
    public $requestParams;

    protected $handlerIncstance;

    public function init()
    {
        $class = $this->handlerClass;
        $requester = new $class([
            'baseUrl' => $this->requsetParams['url'],
            'headers' => [
                'Accept' => 'application/json',
                'Trackingmore-Api-Key' => $this->requsetParams['token'],
            ],
        ]);

        // Decorate with request operation registrator
        $this->handlerInstance = new RequesterRegistrator($requester)
    }

    public function getRequester()
    {
        return $this->handlerInstance;
    }
}
