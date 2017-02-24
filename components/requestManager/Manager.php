<?php
namespace app\components\requestManager;

class  Manager extends \yii\base\Component
{
    public $handlerClass;

    public function getRequester($requestParams)
    {
        $class = $this->handlerClass;
        $requester = new $class([
            'baseUrl' => $requestParams['url'],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Trackingmore-Api-Key' => $requestParams['token'],
            ],
        ]);

        return new RequesterRegistrator($requester);
    }
}
